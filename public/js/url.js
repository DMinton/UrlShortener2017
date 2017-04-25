
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

                if (!vm.inputUrl.match(/^http([s]?):\/\/.*/)) {
                    vm.inputUrl = 'http://' + vm.inputUrl;
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
        validateUrl: function(url) {
            var rg_pctEncoded = "%[0-9a-fA-F]{2}";
            var rg_protocol = "(http|https):\\/\\/";

            var rg_userinfo = "([a-zA-Z0-9$\\-_.+!*'(),;:&=]|" + rg_pctEncoded + ")+" + "@";

            var rg_decOctet = "(25[0-5]|2[0-4][0-9]|[0-1][0-9][0-9]|[1-9][0-9]|[0-9])"; // 0-255
            var rg_ipv4address = "(" + rg_decOctet + "(\\." + rg_decOctet + "){3}" + ")";
            var rg_hostname = "([a-zA-Z0-9\\-\\u00C0-\\u017F]+\\.)+([a-zA-Z]{2,})";
            var rg_port = "[0-9]+";

            var rg_hostport = "(" + rg_ipv4address + "|localhost|" + rg_hostname + ")(:" + rg_port + ")?";

            // chars sets
            // safe           = "$" | "-" | "_" | "." | "+"
            // extra          = "!" | "*" | "'" | "(" | ")" | ","
            // hsegment       = *[ alpha | digit | safe | extra | ";" | ":" | "@" | "&" | "=" | escape ]
            var rg_pchar = "a-zA-Z0-9$\\-_.+!*'(),;:@&=";
            var rg_segment = "([" + rg_pchar + "]|" + rg_pctEncoded + ")*";

            var rg_path = rg_segment + "(\\/" + rg_segment + ")*";
            var rg_query = "\\?" + "([" + rg_pchar + "/?]|" + rg_pctEncoded + ")*";
            var rg_fragment = "\\#" + "([" + rg_pchar + "/?]|" + rg_pctEncoded + ")*";

            var rgHttpUrl = new RegExp( 
                "^"
                + rg_protocol
                + "(" + rg_userinfo + ")?"
                + rg_hostport
                + "(\\/"
                + "(" + rg_path + ")?"
                + "(" + rg_query + ")?"
                + "(" + rg_fragment + ")?"
                + ")?"
                + "$"
            );

            if (rgHttpUrl.test(url)) {
                return true;
            } else {
                return false;
            }
        }
    }
});

vm.fetchTopVisits();
setInterval(vm.fetchTopVisits, 5000);