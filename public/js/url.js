
var vm = new Vue({
    el: '#urlform',
    data: {
        inputUrl: '',
        displayUrl: '',
        errorMessage: '',
        sites: {}
    },
    computed: {
        urlIsEmptyAndNotValid: function(event) {
            if (this.inputUrl.length === 0 && !this.validateUrl(this.inputUrl)) {
                this.displayUrl = ''
                return true;
            }

            this.errorMessage = ''

            return false;
        }
    },
    methods: {
        submitUrl: function (event) {
            vm = this;
            vm.errorMessage = ''
            Vue.nextTick(function () {
                if (!vm.validateUrl(vm.inputUrl)) {
                    vm.errorMessage = "Please enter a valid URL.";
                    return false;
                }

                $.ajax({
                    type: "POST",
                    url: '/api/create',
                    data: {url: vm.inputUrl},
                    success: function(data) {
                        vm.displayUrl = document.location.href + data.url.shortenedUrl;
                        vm.fetchTopVisits();
                    }
                });
            });
        },
        fetchTopVisits: function () {
            vm = this;
            $.ajax({
                type: "GET",
                url: '/api/topVisits/5',
                success: function(data) {
                    if (data.topVisits && !$.isEmptyObject(data.topVisits)) {
                        vm.sites = data.topVisits;
                    }
                }
            });
        },
        clearUrl: function() {
            this.displayUrl = '';
            this.inputUrl = '';
            this.errorMessage = '';
        },
        populateUrl: function(site) {
            this.inputUrl = site.fullUrl;
            this.displayUrl = document.location.href + site.shortenedUrl;
        },
        validateUrl: function(value) {
            return /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(value);
        }
    }
});

vm.fetchTopVisits();
setInterval(vm.fetchTopVisits, 5000);