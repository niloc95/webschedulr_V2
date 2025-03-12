<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @webSchedulr - Online Appointment Scheduler
 *
 * @package     @webSchedulr
 * @author      N. Cara <nilo.cara@frontend.co.za>
 * @copyright   Copyright (c) Nilo Cara
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://webschedulr.co.za
 * @since       v1.4.0
 */

use Gregwar\Captcha\PhraseBuilder;
use Gregwar\Captcha\PhraseBuilderInterface;

/**
 * Class Captcha_builder
 *
 * This class replaces the Gregwar\Captcha\CaptchaBuilder so that it becomes PHP 8.1 compatible.
 */
class Captcha_builder
{
    /**
     * Temporary dir, for OCR check
     */
    public string $tempDir = 'temp/';

    /**
     * @var array
     */
    protected array $fingerprint = [];

    /**
     * @var bool
     */
    protected bool $useFingerprint = false;

    /**
     * @var array|null
     */
    protected ?array $textColor = [];

    /**
     * @var array|null
     */
    protected ?array $lineColor = null;

    /**
     * @var array|null
     */
    protected ?array $background = null;

    /**
     * @var array|null
     */
    protected ?array $backgroundColor = null;

    /**
     * @var array
     */
    protected array $backgroundImages = [];

    /**
     * @var resource|null
     */
    protected $contents = null;

    /**
     * @var string|null
     */
    protected ?string $phrase = null;

    /**
     * @var PhraseBuilderInterface
     */
    protected PhraseBuilderInterface $builder;

    /**
     * @var bool
     */
    protected bool $distortion = true;

    /**
     * The maximum number of lines to draw in front of the image.
     */
    protected ?int $maxFrontLines = null;

    /**
     * The maximum number of lines to draw behind the image.
     */
    protected ?int $maxBehindLines = null;

    /**
     * The maximum angle of char.
     */
    protected int $maxAngle = 8;

    /**
     * The maximum offset of char.
     */
    protected int $maxOffset = 5;

    /**
     * Is the interpolation enabled?
     */
    protected bool $interpolation = true;

    /**
     * Ignore all effects.
     */
    protected bool $ignoreAllEffects = false;

    /**
     * Allowed image types for the background images.
     */
    protected array $allowedBackgroundImageTypes = ['image/png', 'image/jpeg', 'image/gif'];

    /**
     * Constructor.
     *
     * @param string|null $phrase
     * @param PhraseBuilderInterface|null $builder
     */
    public function __construct(?string $phrase = null, ?PhraseBuilderInterface $builder = null)
    {
        $this->builder = $builder ?? new PhraseBuilder();
        $this->phrase = $phrase ?? $this->builder->build();
    }

    /**
     * Generate the image.
     *
     * @param int $width
     * @param int $height
     * @param string|null $font
     * @param array|null $fingerprint
     * @return $this
     * @throws Exception
     */
    public function build(int $width = 150, int $height = 40, ?string $font = null, ?array $fingerprint = null): self
    {
        if ($fingerprint !== null) {
            $this->fingerprint = $fingerprint;
            $this->useFingerprint = true;
        } else {
            $this->fingerprint = [];
            $this->useFingerprint = false;
        }

        if ($font === null) {
            $font = __DIR__ . '/../../vendor/gregwar/captcha/src/Gregwar/Captcha/Font/captcha' . $this->rand(0, 5) . '.ttf';
        }

        $bg = null;

        if (empty($this->backgroundImages)) {
            // Create a blank image with a background color
            $image = imagecreatetruecolor($width, $height);
            if ($this->backgroundColor === null) {
                $bg = imagecolorallocate($image, $this->rand(200, 255), $this->rand(200, 255), $this->rand(200, 255));
            } else {
                $color = $this->backgroundColor;
                $bg = imagecolorallocate($image, $color[0], $color[1], $color[2]);
            }
            $this->background = $bg;
            imagefill($image, 0, 0, $bg);
        } else {
            // Use a random background image
            $randomBackgroundImage = $this->backgroundImages[array_rand($this->backgroundImages)];
            $imageType = $this->validateBackgroundImage($randomBackgroundImage);
            $image = $this->createBackgroundImageFromType($randomBackgroundImage, $imageType);
        }

        // Apply effects
        if (!$this->ignoreAllEffects) {
            $square = $width * $height;
            $effects = $this->rand($square / 3000, $square / 2000);

            if ($this->maxBehindLines !== null && $this->maxBehindLines > 0) {
                $effects = min($this->maxBehindLines, $effects);
            }

            if ($this->maxBehindLines !== 0) {
                for ($e = 0; $e < $effects; $e++) {
                    $this->drawLine($image, $width, $height);
                }
            }
        }

        // Write CAPTCHA text
        $color = $this->writePhrase($image, $this->phrase, $font, $width, $height);

        // Apply front effects
        if (!$this->ignoreAllEffects) {
            $square = $width * $height;
            $effects = $this->rand($square / 3000, $square / 2000);

            if ($this->maxFrontLines !== null && $this->maxFrontLines > 0) {
                $effects = min($this->maxFrontLines, $effects);
            }

            if ($this->maxFrontLines !== 0) {
                for ($e = 0; $e < $effects; $e++) {
                    $this->drawLine($image, $width, $height, $color);
                }
            }
        }

        // Distort the image
        if ($this->distortion && !$this->ignoreAllEffects) {
            $image = $this->distort($image, $width, $height, $bg);
        }

        // Post effects
        if (!$this->ignoreAllEffects) {
            $this->postEffect($image);
        }

        $this->contents = $image;
        return $this;
    }

    /**
     * Returns a random number or the next number in the fingerprint.
     *
     * @param int $min
     * @param int $max
     * @return int
     */
    protected function rand(int $min, int $max): int
    {
        if ($this->useFingerprint) {
            $value = current($this->fingerprint);
            next($this->fingerprint);
        } else {
            $value = mt_rand($min, $max);
            $this->fingerprint[] = $value;
        }
        return $value;
    }

    /**
     * Validate the background image path.
     *
     * @param string $backgroundImage
     * @return string
     * @throws Exception
     */
    protected function validateBackgroundImage(string $backgroundImage): string
    {
        if (!file_exists($backgroundImage)) {
            throw new Exception("Background image file does not exist: {$backgroundImage}");
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $imageType = finfo_file($finfo, $backgroundImage);
        finfo_close($finfo);

        if (!in_array($imageType, $this->allowedBackgroundImageTypes)) {
            throw new Exception("Unsupported background image type: {$imageType}");
        }

        return $imageType;
    }

    /**
     * Create background image from type.
     *
     * @param string $backgroundImage
     * @param string $imageType
     * @return resource
     * @throws Exception
     */
    protected function createBackgroundImageFromType(string $backgroundImage, string $imageType)
    {
        switch ($imageType) {
            case 'image/jpeg':
                return imagecreatefromjpeg($backgroundImage);
            case 'image/png':
                return imagecreatefrompng($backgroundImage);
            case 'image/gif':
                return imagecreatefromgif($backgroundImage);
            default:
                throw new Exception("Unsupported background image type: {$imageType}");
        }
    }

    /**
     * Draw lines over the image.
     *
     * @param resource $image
     * @param int $width
     * @param int $height
     * @param int|null $tcol
     */
    protected function drawLine($image, int $width, int $height, ?int $tcol = null): void
    {
        if ($this->lineColor === null) {
            $red = $this->rand(100, 255);
            $green = $this->rand(100, 255);
            $blue = $this->rand(100, 255);
        } else {
            $red = $this->lineColor[0];
            $green = $this->lineColor[1];
            $blue = $this->lineColor[2];
        }

        if ($tcol === null) {
            $tcol = imagecolorallocate($image, $red, $green, $blue);
        }

        if ($this->rand(0, 1)) {
            // Horizontal line
            $Xa = $this->rand(0, $width / 2);
            $Ya = $this->rand(0, $height);
            $Xb = $this->rand($width / 2, $width);
            $Yb = $this->rand(0, $height);
        } else {
            // Vertical line
            $Xa = $this->rand(0, $width);
            $Ya = $this->rand(0, $height / 2);
            $Xb = $this->rand(0, $width);
            $Yb = $this->rand($height / 2, $height);
        }

        imagesetthickness($image, $this->rand(1, 3));
        imageline($image, $Xa, $Ya, $Xb, $Yb, $tcol);
    }

    /**
     * Writes the phrase on the image.
     *
     * @param resource $image
     * @param string $phrase
     * @param string $font
     * @param int $width
     * @param int $height
     * @return int
     */
    protected function writePhrase($image, string $phrase, string $font, int $width, int $height): int
    {
        $length = mb_strlen($phrase);
        if ($length === 0) {
            return imagecolorallocate($image, 0, 0, 0);
        }

        $size = (int) round($width / $length) - $this->rand(0, 3) - 1;
        $box = imagettfbbox($size, 0, $font, $phrase);
        $textWidth = $box[2] - $box[0];
        $textHeight = $box[1] - $box[7];
        $x = (int) round(($width - $textWidth) / 2);
        $y = (int) round(($height - $textHeight) / 2) + $size;

        if (!$this->textColor) {
            $textColor = [$this->rand(0, 150), $this->rand(0, 150), $this->rand(0, 150)];
        } else {
            $textColor = $this->textColor;
        }

        $col = imagecolorallocate($image, $textColor[0], $textColor[1], $textColor[2]);

        for ($i = 0; $i < $length; $i++) {
            $symbol = mb_substr($phrase, $i, 1);
            $box = imagettfbbox($size, 0, $font, $symbol);
            $w = $box[2] - $box[0];
            $angle = $this->rand(-$this->maxAngle, $this->maxAngle);
            $offset = $this->rand(-$this->maxOffset, $this->maxOffset);
            imagettftext($image, $size, $angle, $x, $y + $offset, $col, $font, $symbol);
            $x += $w;
        }

        return $col;
    }

    /**
     * Distorts the image.
     *
     * @param resource $image
     * @param int $width
     * @param int $height
     * @param int $bg
     * @return resource
     */
    public function distort($image, int $width, int $height, int $bg)
    {
        $contents = imagecreatetruecolor($width, $height);
        $X = $this->rand(0, $width);
        $Y = $this->rand(0, $height);
        $phase = $this->rand(0, 10);
        $scale = 1.1 + $this->rand(0, 10000) / 30000;

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $Vx = $x - $X;
                $Vy = $y - $Y;
                $Vn = sqrt($Vx * $Vx + $Vy * $Vy);

                if ($Vn != 0) {
                    $Vn2 = $Vn + 4 * sin($Vn / 30);
                    $nX = $X + ($Vx * $Vn2) / $Vn;
                    $nY = $Y + ($Vy * $Vn2) / $Vn;
                } else {
                    $nX = $X;
                    $nY = $Y;
                }

                $nY = $nY + $scale * sin($phase + $nX * 0.2);

                if ($this->interpolation) {
                    $p = $this->interpolate(
                        $nX - floor($nX),
                        $nY - floor($nY),
                        $this->getCol($image, floor($nX), floor($nY), $bg),
                        $this->getCol($image, ceil($nX), floor($nY), $bg),
                        $this->getCol($image, floor($nX), ceil($nY), $bg),
                        $this->getCol($image, ceil($nX), ceil($nY), $bg)
                    );
                } else {
                    $p = $this->getCol($image, round($nX), round($nY), $bg);
                }

                if ($p == 0) {
                    $p = $bg;
                }

                imagesetpixel($contents, $x, $y, $p);
            }
        }

        return $contents;
    }

    /**
     * Interpolates the color.
     *
     * @param float $x
     * @param float $y
     * @param int $nw
     * @param int $ne
     * @param int $sw
     * @param int $se
     * @return int
     */
    protected function interpolate(float $x, float $y, int $nw, int $ne, int $sw, int $se): int
    {
        [$r0, $g0, $b0] = $this->getRGB($nw);
        [$r1, $g1, $b1] = $this->getRGB($ne);
        [$r2, $g2, $b2] = $this->getRGB($sw);
        [$r3, $g3, $b3] = $this->getRGB($se);

        $cx = 1.0 - $x;
        $cy = 1.0 - $y;

        $m0 = $cx * $r0 + $x * $r1;
        $m1 = $cx * $r2 + $x * $r3;
        $r = (int) ($cy * $m0 + $y * $m1);

        $m0 = $cx * $g0 + $x * $g1;
        $m1 = $cx * $g2 + $x * $g3;
        $g = (int) ($cy * $m0 + $y * $m1);

        $m0 = $cx * $b0 + $x * $b1;
        $m1 = $cx * $b2 + $x * $b3;
        $b = (int) ($cy * $m0 + $y * $m1);

        return ($r << 16) | ($g << 8) | $b;
    }

    /**
     * Gets the RGB values from a color.
     *
     * @param int $col
     * @return array
     */
    protected function getRGB(int $col): array
    {
        return [($col >> 16) & 0xFF, ($col >> 8) & 0xFF, $col & 0xFF];
    }

    /**
     * Gets the color at a specific position.
     *
     * @param resource $image
     * @param int $x
     * @param int $y
     * @param int $background
     * @return int
     */
    protected function getCol($image, int $x, int $y, int $background): int
    {
        $L = imagesx($image);
        $H = imagesy($image);
        if ($x < 0 || $x >= $L || $y < 0 || $y >= $H) {
            return $background;
        }

        return imagecolorat($image, $x, $y);
    }

    /**
     * Apply post effects to the image.
     *
     * @param resource $image
     */
    protected function postEffect($image): void
    {
        if (!function_exists('imagefilter')) {
            return;
        }

        if ($this->backgroundColor !== null || $this->textColor !== null) {
            return;
        }

        // Negate
        if ($this->rand(0, 1) == 0) {
            imagefilter($image, IMG_FILTER_NEGATE);
        }

        // Edge detection
        if ($this->rand(0, 10) == 0) {
            imagefilter($image, IMG_FILTER_EDGEDETECT);
        }

        // Contrast
        imagefilter($image, IMG_FILTER_CONTRAST, $this->rand(-50, 10));

        // Colorize
        if ($this->rand(0, 5) == 0) {
            imagefilter($image, IMG_FILTER_COLORIZE, $this->rand(-80, 50), $this->rand(-80, 50), $this->rand(-80, 50));
        }
    }

    /**
     * Instantiation.
     *
     * @param string|null $phrase
     * @return self
     */
    public static function create(?string $phrase = null): self
    {
        return new self($phrase);
    }

    /**
     * Gets the image contents.
     *
     * @return resource|null
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Sets the interpolation.
     *
     * @param bool $interpolate
     * @return $this
     */
    public function setInterpolation(bool $interpolate = true): self
    {
        $this->interpolation = $interpolate;
        return $this;
    }

    /**
     * Sets the distortion.
     *
     * @param bool $distortion
     * @return $this
     */
    public function setDistortion(bool $distortion): self
    {
        $this->distortion = $distortion;
        return $this;
    }

    /**
     * Sets the maximum number of lines behind the text.
     *
     * @param int|null $maxBehindLines
     * @return $this
     */
    public function setMaxBehindLines(?int $maxBehindLines): self
    {
        $this->maxBehindLines = $maxBehindLines;
        return $this;
    }

    /**
     * Sets the maximum number of lines in front of the text.
     *
     * @param int|null $maxFrontLines
     * @return $this
     */
    public function setMaxFrontLines(?int $maxFrontLines): self
    {
        $this->maxFrontLines = $maxFrontLines;
        return $this;
    }

    /**
     * Sets the maximum angle of characters.
     *
     * @param int $maxAngle
     * @return $this
     */
    public function setMaxAngle(int $maxAngle): self
    {
        $this->maxAngle = $maxAngle;
        return $this;
    }

    /**
     * Sets the maximum offset of characters.
     *
     * @param int $maxOffset
     * @return $this
     */
    public function setMaxOffset(int $maxOffset): self
    {
        $this->maxOffset = $maxOffset;
        return $this;
    }

    /**
     * Sets the text color.
     *
     * @param int $r
     * @param int $g
     * @param int $b
     * @return $this
     */
    public function setTextColor(int $r, int $g, int $b): self
    {
        $this->textColor = [$r, $g, $b];
        return $this;
    }

    /**
     * Sets the background color.
     *
     * @param int $r
     * @param int $g
     * @param int $b
     * @return $this
     */
    public function setBackgroundColor(int $r, int $g, int $b): self
    {
        $this->backgroundColor = [$r, $g, $b];
        return $this;
    }

    /**
     * Sets the line color.
     *
     * @param int $r
     * @param int $g
     * @param int $b
     * @return $this
     */
    public function setLineColor(int $r, int $g, int $b): self
    {
        $this->lineColor = [$r, $g, $b];
        return $this;
    }

    /**
     * Sets the ignoreAllEffects flag.
     *
     * @param bool $ignoreAllEffects
     * @return $this
     */
    public function setIgnoreAllEffects(bool $ignoreAllEffects): self
    {
        $this->ignoreAllEffects = $ignoreAllEffects;
        return $this;
    }

    /**
     * Sets the background images.
     *
     * @param array $backgroundImages
     * @return $this
     */
    public function setBackgroundImages(array $backgroundImages): self
    {
        $this->backgroundImages = $backgroundImages;
        return $this;
    }

    /**
     * Builds the CAPTCHA while ensuring it is not readable by OCR.
     *
     * @param int $width
     * @param int $height
     * @param string|null $font
     * @param array|null $fingerprint
     * @throws Exception
     */
    public function buildAgainstOCR(int $width = 150, int $height = 40, ?string $font = null, ?array $fingerprint = null): void
    {
        do {
            $this->build($width, $height, $font, $fingerprint);
        } while ($this->isOCRReadable());
    }

    /**
     * Checks if the CAPTCHA is readable by OCR.
     *
     * @return bool
     * @throws Exception
     */
    public function isOCRReadable(): bool
    {
        if (!is_dir($this->tempDir)) {
            if (!@mkdir($this->tempDir, 0755, true)) {
                throw new Exception("Failed to create temporary directory: {$this->tempDir}");
            }
        }

        $tempj = $this->tempDir . uniqid('captcha', true) . '.jpg';
        $tempp = $this->tempDir . uniqid('captcha', true) . '.pgm';

        $this->save($tempj);
        shell_exec("convert $tempj $tempp");
        $value = trim(strtolower(shell_exec("ocrad $tempp")));

        @unlink($tempj);
        @unlink($tempp);

        return $this->testPhrase($value);
    }

    /**
     * Saves the CAPTCHA to a file.
     *
     * @param string $filename
     * @param int $quality
     */
    public function save(string $filename, int $quality = 90): void
    {
        imagejpeg($this->contents, $filename, $quality);
    }

    /**
     * Tests if the given phrase matches the CAPTCHA phrase.
     *
     * @param string $phrase
     * @return bool
     */
    public function testPhrase(string $phrase): bool
    {
        return $this->builder->niceize($phrase) === $this->builder->niceize($this->getPhrase());
    }

    /**
     * Gets the CAPTCHA phrase.
     *
     * @return string
     */
    public function getPhrase(): string
    {
        return $this->phrase;
    }

    /**
     * Sets the CAPTCHA phrase.
     *
     * @param string $phrase
     * @return $this
     */
    public function setPhrase(string $phrase): self
    {
        $this->phrase = $phrase;
        return $this;
    }

    /**
     * Gets the image GD resource.
     *
     * @return resource|null
     */
    public function getGd()
    {
        return $this->contents;
    }

    /**
     * Gets the HTML inline base64 representation of the CAPTCHA.
     *
     * @param int $quality
     * @return string
     */
    public function inline(int $quality = 90): string
    {
        return 'data:image/jpeg;base64,' . base64_encode($this->get($quality));
    }

    /**
     * Gets the image contents as a string.
     *
     * @param int $quality
     * @return string
     */
    public function get(int $quality = 90): string
    {
        ob_start();
        $this->output($quality);
        return ob_get_clean();
    }

    /**
     * Outputs the image to the browser.
     *
     * @param int $quality
     */
    public function output(int $quality = 90): void
    {
        imagejpeg($this->contents, null, $quality);
    }

    /**
     * Gets the fingerprint.
     *
     * @return array
     */
    public function getFingerprint(): array
    {
        return $this->fingerprint;
    }
}