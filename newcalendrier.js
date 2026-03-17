
// Configuration initiale
let currentDate = new Date();
let currentMonth = currentDate.getMonth();
let currentYear = currentDate.getFullYear();
const monthNames = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin",
    "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];

// Initialisation
document.addEventListener('DOMContentLoaded', function () {
    generateCalendar(currentMonth, currentYear);

    // Gestionnaires d'événements pour les flèches
    document.getElementById('prevMonth').addEventListener('click', () => changeMonth(-1));
    document.getElementById('nextMonth').addEventListener('click', () => changeMonth(1));
});

// Fonction pour changer de mois
function changeMonth(offset) {
    currentMonth += offset;

    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    } else if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }

    generateCalendar(currentMonth, currentYear);
}
function generateCalendar(month, year) {
    const calendar = document.getElementById('calendar');
    if (!calendar) return;

    calendar.innerHTML = '';

    // En-têtes des jours
    const daysOfWeek = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
    const headerRow = document.createElement('div');
    headerRow.className = 'calendar-header';

    daysOfWeek.forEach(day => {
        const dayElement = document.createElement('div');
        dayElement.textContent = day;
        headerRow.appendChild(dayElement);
    });

    calendar.appendChild(headerRow);

    // Calcul des jours du mois
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const daysInMonth = lastDay.getDate();
    const startingDay = firstDay.getDay();

    let date = 1;
    for (let i = 0; i < 6; i++) {
        const weekRow = document.createElement('div');
        weekRow.className = 'calendar-week';

        for (let j = 0; j < 7; j++) {
            const dayCell = document.createElement('div');
            dayCell.className = 'day';

            if ((i === 0 && j < startingDay) || date > daysInMonth) {
                dayCell.classList.add('empty');
            }
            else {
                dayCell.innerHTML = `<div class="day-number">${date}</div>`;

                // Highlight du jour actuel
                if (date === currentDate.getDate() &&
                    month === currentDate.getMonth() &&
                    year === currentDate.getFullYear()) {
                    dayCell.classList.add('today');
                }

                // Vérification des événements
                const dayEvents = getEventsForDate(date, month + 1, year);
                if (dayEvents.length > 0) {
                    dayCell.classList.add('has-event');
                    const badge = document.createElement('div');
                    badge.className = 'event-badge';
                    badge.textContent = dayEvents.length;
                    dayCell.appendChild(badge);
                }

                // Ajoutez le gestionnaire de clic pour TOUS les jours (avec ou sans événement)
                dayCell.addEventListener('click', function () {
                    showEventDetails(dayEvents);
                });
                date++;
            }

            weekRow.appendChild(dayCell);
        }

        calendar.appendChild(weekRow);
        if (date > daysInMonth) break;
    }

    document.getElementById('currentMonth').textContent = `${monthNames[month]} ${year}`;
}

function getEventsForDate(day, month, year) {
    const targetDate = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
    return events.filter(event => event.js_date === targetDate);
}

function showEventDetails(dayEvents) {
    const contentPanel = document.getElementById('eventDetailsContent');
    if (!contentPanel) return;

    contentPanel.innerHTML = '';

    if (!dayEvents || dayEvents.length === 0) {
        contentPanel.innerHTML = '<p>Aucun événement prévu ce jour</p>';
        return;
    }

    dayEvents.forEach(event => {
        const eventElement = document.createElement('div');
        eventElement.className = 'event-item';

        const startDate = new Date(event.date_debut);
        const endDate = event.date_fin ? new Date(event.date_fin) : null;

        eventElement.innerHTML = `
                <h4>${event.titre || 'Sans titre'}</h4>
                <p><strong>Date :</strong> ${startDate.toLocaleString('fr-FR')}</p>
                ${endDate ? `<p><strong>Fin :</strong> ${endDate.toLocaleString('fr-FR')}</p>` : ''}
                <p><strong>Lieu :</strong> ${event.lieu || 'Non spécifié'}</p>
                <p>${event.description || 'Pas de description'}</p>
                ${event.image_url ? `<img src="${event.image_url}" alt="${event.titre}" style="max-width:100%">` : ''}
                <hr>
            `;
        contentPanel.appendChild(eventElement);
    });
}

function changeMonth(offset) {
    currentMonth += offset;

    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    } else if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }

    generateCalendar(currentMonth, currentYear);
}
