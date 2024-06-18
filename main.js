new Vue({
    el: '#app',
    data: {
        results: [],
        errorMessages: []
    },
    created() {
        this.fetchData();
    },
    methods: {
        fetchData() {
            fetch('http://localhost:3000/check-artist-free')
                .then(response => response.json())
                .then(data => {
                    this.results = data.results;
                    data.results.forEach(result => {
                        if (result.error) {
                            this.showError(result.error);
                        }
                    });
                })
                .catch(error => {
                    //console.error('Error fetching data:', error);
                    this.showError('Failed to fetch data');
                });
        },
        showError(message) {
            this.errorMessages.push(message);
            setTimeout(() => {
                this.errorMessages.shift();
            }, 3000);
        }
    }
});
