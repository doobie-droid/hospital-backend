<head>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <link rel="icon" type="image/png" sizes="16x16" href="https://clafiya.com/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="https://clafiya.com/favicon-32x32.png">
    <title>We are Clafiya!!</title>
    <!------ Include the above in your HEAD tag ---------->
</head>

<body>
    <style>
        /* Methods */
        .method .header,
        .method .cell {
            padding: 6px 6px 6px 10px;
        }

        .method .list-header .header {
            font-weight: normal;
            text-transform: uppercase;
            font-size: 0.8em;
            color: #999;
            background-color: #eee;
        }

        .method [class^="row"],
        .method [class*=" row"] {
            border-bottom: 1px solid #ddd;
        }

        .method [class^="row"]:hover,
        .method [class*=" row"]:hover {
            background-color: #f7f7f7;
        }

        .method .cell {
            font-size: 0.85em;
        }

        .method .cell .mobile-isrequired {
            display: none;
            font-weight: normal;
            text-transform: uppercase;
            color: #aaa;
            font-size: 0.8em;
        }

        .method .cell .propertyname {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .method .cell .type {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .method .cell code {
            color: #428bca;
        }

        .method .cell a,
        .method .cell a:hover {
            text-decoration: none;
        }

        .method .cell code.custom {
            color: #8a6d3b;
            text-decoration: none;
        }

        .method .cell .text-muted {
            color: #ddd;
        }

        @media (max-width: 991px) {

            .method [class^="row"],
            .method [class*=" row"] {
                padding-top: 10px;
                padding-bottom: 10px;
            }

            .method .cell {
                padding: 0 10px;
            }

            .method .cell .propertyname {
                font-weight: bold;
                font-size: 1.2em;
            }

            .method .cell .propertyname .lookuplink {
                font-weight: normal;
                font-size: 1.5em;
                position: absolute;
                top: 0;
                right: 10px;
            }

            .method .cell .type {
                padding-left: 10px;
                font-size: 1.1em;
            }

            .method .cell .isrequired {
                padding-left: 10px;
                display: none;
            }

            .method .cell .description {
                padding-left: 10px;
            }

            .method .cell .mobile-isrequired {
                display: inline;
            }
        }


        /* Row Utilities */
        [class^='row'].margin-0,
        [class*=' row'].margin-0,
        [class^='form-group'].margin-0,
        [class*=' form-group'].margin-0 {
            margin-left: -0px;
            margin-right: -0px;
        }

        [class^='row'].margin-0>[class^='col-'],
        [class^='row'].margin-0>[class*=' col-'],
        [class*=' row'].margin-0>[class^='col-'],
        [class*=' row'].margin-0>[class*=' col-'],
        [class^='form-group'].margin-0>[class^='col-'],
        [class^='form-group'].margin-0>[class*=' col-'],
        [class*=' form-group'].margin-0>[class^='col-'],
        [class*=' form-group'].margin-0>[class*=' col-'] {
            padding-right: 0px;
            padding-left: 0px;
        }

        [class^='row'].margin-0 [class^='row'],
        [class^='row'].margin-0 [class*=' row'],
        [class^='row'].margin-0 [class^='form-group'],
        [class^='row'].margin-0 [class*=' form-group'],
        [class*=' row'].margin-0 [class^='row'],
        [class*=' row'].margin-0 [class*=' row'],
        [class*=' row'].margin-0 [class^='form-group'],
        [class*=' row'].margin-0 [class*=' form-group'],
        [class^='form-group'].margin-0 [class^='row'],
        [class^='form-group'].margin-0 [class*=' row'],
        [class^='form-group'].margin-0 [class^='form-group'],
        [class^='form-group'].margin-0 [class*=' form-group'],
        [class*=' form-group'].margin-0 [class^='row'],
        [class*=' form-group'].margin-0 [class*=' row'],
        [class*=' form-group'].margin-0 [class^='form-group'],
        [class*=' form-group'].margin-0 [class*=' form-group'] {
            margin-left: 0;
            margin-right: 0;
        }
    </style>
    <div class="container">

        <h2>Hospital Api for booking Appointments</h2>
        <p class="lead">
            This Api allows patients to book appointments in a hospital and pay for those appointments.<br> It does the
            following:
        </p>

        <div class="alert alert-info">
            <ul>
                <li>Login and registration for patients using Json Web Tokens</li>
                <li>Appointment booking for patients</li>
                <li>Payment integration using both paystack and flutterwave</li>
                <li>Webhooks to listen to flutterwave and paystacks api for payment confirmation</li>
                <li>Appointments status changes to paid upon payment confirmation</li>
                <li>Email for each important events because communication is key and we love our customers</li>
                <li>Of course, we record the payments</li>
                <li><s>Unit Tests for All the endpoints, lol, this part is cancelled cause unit tests aren't the
                        api😁😁<s></li>




        </div>

        <hr />


    </div>
</body>
