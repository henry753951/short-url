<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Panel</title>
    <script src="https://unpkg.com/vue@3"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
</head>
<style>
    *,
    html {
        margin: 0;
        padding: 0;
    }

    .disclaimer {
        display: none;
    }
</style>

<body>
    <div id="app">
        <div class="container">
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex flex-wrap flex-md-nowrap" v-for="item in list">
                    <div class="w-100">
                        <p>IP: {{item.data.ip}}</p>
                        <p>OS: {{item.data.os}}</p>
                        <p>Browser: {{item.data.browser}}</p>
                        <p v-if="item.data.pos">GPS: {{Math.round(item.data.pos.lon*100)/100}} | {{Math.round(item.data.pos.lat*100)/100}}</p>
                        <p v-if="item.data.pos">Accuracy: {{Math.round(item.data.pos.accuracy*10)/10}} m</p>
                        <p>time: {{item.data.time}}</p>
                    </div>
                    <iframe v-if="item.data.pos" width="100%" height="320" frameborder="0" scrolling="yes" marginheight="0" marginwidth="0" 
                        :src="'https://maps.google.com/maps?q='+item.data.pos.lat+','+item.data.pos.lon+'&amp;output=embed'">
                    </iframe>
                </li>
            </ul>
        </div>
    </div>

    <script>
        var app = Vue.createApp({
            data() {
                return {
                    list: []
                };
            },
            methods: {
                async copy(s) {
                    await navigator.clipboard.writeText(s);
                    alert('Copied!');
                },
                getData: function() {
                    var data = {
                        id: <?php echo $_GET["id"]; ?>,
                    }
                    fetch('/api/getVisitorData.php', {
                            method: 'POST',
                            body: JSON.stringify(data),
                        })
                        .then(response => response.json())
                        .then(json => this.list = json.list);
                },
            },
            mounted() {
                this.getData();
            }
        }).mount("#app");
    </script>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>

</html>