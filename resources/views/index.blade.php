<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <script type="text/javascript" src="/assets/dist/D3/d3.min.js"></script>
    <style type="text/css">
        .chart {
            display: block;
            margin: auto;
            margin-top: 40px;
        }

        text {
            font-size: 11px;
        }

        rect {
            fill: none;
        }
    </style>
</head>
<body>
<div id="body">
</div>

<script src="/assets/js/index.js"></script>
<script src="/assets/dist/jquery.min.js"></script>
<script type="text/javascript">
    $(function () {
        d3.json('/quests/tree', function (data) {
            renderMap(data['data']);
        });
    });
</script>
</body>
</html>
