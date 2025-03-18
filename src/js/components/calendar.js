export class Calendar {
  constructor(element, options = {}) {
    this.element = element;
    this.options = Object.assign({
      date: new Date(),
      onDateClick: null,
      onEventClick: null,
      events: []
    }, options);
    
    this.currentMonth = this.options.date.getMonth();
    this.currentYear = this.options.date.getFullYear();
    
    this.render();
    this.bindEvents();
  }
  
  render() {
    const year = this.currentYear;
    const month = this.currentMonth;
    
    // Create calendar header
    const header = document.createElement('div');
    header.className = 'calendar-header';
    header.innerHTML = `
      <button class="calendar-prev"><i class="bi bi-chevron-left"></i></button>
      <h2 class="calendar-title">${new Date(year, month).toLocaleString('default', { month: 'long', year: 'numeric' })}</h2>
      <button class="calendar-next"><i class="bi bi-chevron-right"></i></button>
    `;
    
    // Create calendar grid
    const grid = document.createElement('div');
    grid.className = 'calendar-grid';
    
    // Add weekday headers
    const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    weekdays.forEach(day => {
      const dayEl = document.createElement('div');
      dayEl.className = 'calendar-weekday';
      dayEl.textContent = day;
      grid.appendChild(dayEl);
    });
    
    // Get the first day of the month
    const firstDay = new Date(year, month, 1).getDay();
    
    // Get the number of days in the month
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    
    // Create placeholder cells for days before the 1st of the month
    for (let i = 0; i < firstDay; i++) {
      const dayEl = document.createElement('div');
      dayEl.className = 'calendar-day empty';
      grid.appendChild(dayEl);
    }
    
    // Create cells for each day in the month
    for (let day = 1; day <= daysInMonth; day++) {
      const dayEl = document.createElement('div');
      dayEl.className = 'calendar-day';
      dayEl.setAttribute('data-date', `${year}-${month + 1}-${day}`);
      
      // Check if this is today
      const isToday = this.isToday(year, month, day);
      if (isToday) {
        dayEl.classList.add('today');
      }
      
      // Add the day number
      const dayNumber = document.createElement('span');
      dayNumber.className = 'day-number';
      dayNumber.textContent = day;
      dayEl.appendChild(dayNumber);
      
      // Add events for this day
      const dayEvents = this.getEventsForDay(year, month, day);
      if (dayEvents.length > 0) {
        const eventsContainer = document.createElement('div');
        eventsContainer.className = 'day-events';
        
        dayEvents.forEach(event => {
          const eventEl = document.createElement('div');
          eventEl.className = `event ${event.type || ''}`;
          eventEl.setAttribute('data-event-id', event.id);
          eventEl.textContent = event.title;
          eventsContainer.appendChild(eventEl);
        });
        
        dayEl.appendChild(eventsContainer);
      }
      
      grid.appendChild(dayEl);
    }
    
    // Clear existing content and append new elements
    this.element.innerHTML = '';
    this.element.appendChild(header);
    this.element.appendChild(grid);
  }
  
  bindEvents() {
    // Previous month button
    this.element.querySelector('.calendar-prev').addEventListener('click', () => {
      this.prevMonth();
    });
    
    // Next month button
    this.element.querySelector('.calendar-next').addEventListener('click', () => {
      this.nextMonth();
    });
    
    // Day click events
    this.element.querySelectorAll('.calendar-day:not(.empty)').forEach(day => {
      day.addEventListener('click', (event) => {
        if (event.target.classList.contains('day-number')) {
          const dateStr = day.getAttribute('data-date');
          if (this.options.onDateClick) {
            this.options.onDateClick(dateStr);
          }
        }
      });
    });
    
    // Event click events
    this.element.querySelectorAll('.event').forEach(event => {
      event.addEventListener('click', (e) => {
        e.stopPropagation();
        const eventId = event.getAttribute('data-event-id');
        if (this.options.onEventClick) {
          this.options.onEventClick(eventId);
        }
      });
    });
  }
  
  prevMonth() {
    if (this.currentMonth === 0) {
      this.currentMonth = 11;
      this.currentYear--;
    } else {
      this.currentMonth--;
    }
    this.render();
    this.bindEvents();
  }
  
  nextMonth() {
    if (this.currentMonth === 11) {
      this.currentMonth = 0;
      this.currentYear++;
    } else {
      this.currentMonth++;
    }
    this.render();
    this.bindEvents();
  }
  
  isToday(year, month, day) {
    const today = new Date();
    return year === today.getFullYear() && 
           month === today.getMonth() && 
           day === today.getDate();
  }
  
  getEventsForDay(year, month, day) {
    const date = new Date(year, month, day).toISOString().split('T')[0];
    return this.options.events.filter(event => {
      const eventDate = new Date(event.date).toISOString().split('T')[0];
      return eventDate === date;
    });
  }
  
  setEvents(events) {
    this.options.events = events;
    this.render();
    this.bindEvents();
  }
}
