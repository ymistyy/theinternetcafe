window.addEventListener('load', () => {
    setInterval(() => {
        const date = new Date();
        const displayDate = date.toLocaleDateString();
        const displayTime = date.toLocaleTimeString();
        document.getElementById('datetime').innerHTML = displayDate + " " + displayTime;
    }, 1000);
});

window.addEventListener('load', () => {
    fetch('js/quotes.json')
    .then(response => {
        if (!response.ok) throw new Error('HTTP status ' + response.status);
        return response.json();
    })
    .then(data => {
        const quotes = data.quotes;
        if (!quotes || !quotes.length) throw new Error('No quotes found');
        const randomIndex = Math.floor(Math.random() * quotes.length);
        const randomQuote = quotes[randomIndex];
        document.getElementById('quote').innerHTML = `
            <q>${randomQuote.quote}</q><br>
            <em>— ${randomQuote.author}, <strong>${randomQuote.source}</strong></em>
        `;
    })
    .catch(err => {
        console.error('Error fetching quotes:', err);
        document.getElementById('quote').innerHTML = 'Failed to load quote.';
    });
});


