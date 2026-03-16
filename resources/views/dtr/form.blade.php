<!DOCTYPE html>
<html>

<head>

    <title>DTR Generator Tool</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        body {
            background: #f4f6f9;
            padding: 30px;
        }

        .table input {
            width: 80px;
        }

        .status {
            width: 150px;
        }

        .employee-tabs button {
            margin-right: 5px;
        }

    </style>

</head>

<body>

    <div class="container">

        <h3 class="mb-4">DTR Generator Tool</h3>

        <div class="card p-3 mb-4">

            <div class="row">

                <!-- <div class="col-md-3">
                    <label>Start Date</label>
                    <input type="date" id="start" class="form-control">
                </div>

                <div class="col-md-3">
                    <label>End Date</label>
                    <input type="date" id="end" class="form-control">
                </div> -->
                <div class="col-md-4">

                    <label>Month</label>
                    <input type="month" id="month" class="form-control">

                </div>

                <div class="col-md-4">

                    <label>Cutoff</label>
                    <select id="cutoff" class="form-control">

                        <option value="1">1 - 15</option>
                        <option value="2">16 - End of Month</option>

                    </select>

                </div>
                <div class="col-md-4">

                    <label>Select Employees</label>

                    <select multiple class="form-control" id="employees">
                        @foreach($employees as $emp)
                            <option value="{{ $emp }}">{{ $emp }}</option>
                        @endforeach

                    </select>

                </div>

                <div class="col-md-2 d-flex align-items-end">

                    <button class="btn btn-primary w-100" id="generate">
                        Generate
                    </button>

                </div>

            </div>

        </div>

        <div class="card p-3">

            <div class="employee-tabs mb-3" id="employeeTabs"></div>

            <div class="row">
                <div class="col-md-5">
                    <label>Branch/Dept</label>
                    <input type="text" class="form-control" id="branch">
                </div>
            </div>

            <table class="table table-bordered mt-3">

                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Status</th>
                        <th>AM IN</th>
                        <th>AM OUT</th>
                        <th>PM IN</th>
                        <th>PM OUT</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody id="rows"></tbody>

            </table>

            <button class="btn btn-success mt-3" id="prepareData">
                Prepare DTR Data
            </button>

        </div>

    </div>
    <form id="printForm" method="POST" action="/dtr/print" target="_blank">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="dtr_payload" id="dtr_payload">
    </form>

    <script>
        const holidays = {
            "2026-03-29": "LEGAL HOLIDAY",
            "2026-04-09": "ARAW NG KAGITINGAN"
        }
        let dtrData = {}
        let currentEmployee = null

        function getPeriodDates() {

            let monthVal = $("#month").val()

            if (!monthVal) return null

            let cutoff = $("#cutoff").val()

            let parts = monthVal.split("-")

            let year = parseInt(parts[0])
            let month = parseInt(parts[1])

            let start, end

            if (cutoff == "1") {

                start = new Date(year, month - 1, 1)
                end = new Date(year, month - 1, 15)

            } else {

                start = new Date(year, month - 1, 16)

                // last day of month
                end = new Date(year, month, 0)

            }

            return {
                start,
                end
            }

        }

        // function formatDate(d) {
        //     return d.toISOString().slice(0, 10)
        // }
        function formatDate(date) {

            let y = date.getFullYear()
            let m = String(date.getMonth() + 1).padStart(2, '0')
            let d = String(date.getDate()).padStart(2, '0')

            return `${y}-${m}-${d}`

        }

        function isSunday(date) {
            let d = new Date(date)
            return d.getDay() === 0
        }

        function generateDates(start, end) {

            let dates = []

            let current = new Date(start)
            let last = new Date(end)

            while (current <= last) {

                dates.push(new Date(current))
                current.setDate(current.getDate() + 1)

            }

            return dates

        }

        function defaultRow(date) {

            let row = {

                day: date.getDate(),
                date: formatDate(date),

                status: "",
                am_in: "8:00",
                am_out: "12:00",
                pm_in: "1:00",
                pm_out: "5:00"

            }

            /* Sunday */

            if (isSunday(row.date)) {

                row.status = "SUNDAY"

                row.am_in = ""
                row.am_out = ""
                row.pm_in = ""
                row.pm_out = ""

            }

            /* Holiday */

            if (holidays[row.date]) {

                row.status = "HOLIDAY"
                row.description = holidays[row.date]

                row.am_in = ""
                row.am_out = ""
                row.pm_in = ""
                row.pm_out = ""

            }

            return row

        }

        function renderEmployeeTabs() {

            let html = ""

            Object.keys(dtrData).forEach((emp, i) => {

                html += `
        <button class="btn btn-sm ${i==0?'btn-primary':'btn-secondary'} employee-tab" data-emp="${emp}">
        ${emp}
        </button>
        `

            })

            $("#employeeTabs").html(html)

        }

        function renderRows() {

            let rows = dtrData[currentEmployee];
            let html = "";

            rows.forEach((r, i) => {

                html += `
                    <tr data-row="${i}">

                    <td>${r.day}</td>

                    <td>
                    <select class="form-control status w-100">

                    <option value="">Regular</option>
                    <option value="ABSENT">Absent (Whole Day)</option>
                    <option value="ABSENT_AM">Absent (AM)</option>
                    <option value="ABSENT_PM">Absent (PM)</option>

                    <option value="VL">Vacation Leave (Whole Day)</option>
                    <option value="VL_AM">Vacation Leave (AM)</option>
                    <option value="VL_PM">Vacation Leave (PM)</option>

                    <option value="HOLIDAY">Holiday</option>
                    <option value="NO_DUTY">No Duty</option>
                    <option value="SUNDAY" style="color:red">Sunday</option>

                    </select>
                    </td>

                    <td><input class="form-control am_in" value="${r.am_in ?? ''}"></td>
                    <td><input class="form-control am_out" value="${r.am_out ?? ''}"></td>
                    <td><input class="form-control pm_in" value="${r.pm_in ?? ''}"></td>
                    <td><input class="form-control pm_out" value="${r.pm_out ?? ''}"></td>

                    <td>
                    <button class="btn btn-sm btn-warning apply-all">
                    Apply to All
                    </button>
                    </td>

                    </tr>
                    `;

            });

            $("#rows").html(html);

            $("#rows tr").each(function () {

                let rowIndex = $(this).data("row");
                let r = rows[rowIndex];

                let select = $(this).find(".status");
                select.val(r.status); // ← restore selected value

                applyStatusLogic($(this), r.status);

            });

        }
        $(document).on("click", ".apply-all", function () {

            let rowIndex = $(this).closest("tr").data("row")

            let sourceRow = dtrData[currentEmployee][rowIndex]

            Object.keys(dtrData).forEach(emp => {

                let row = dtrData[emp][rowIndex]

                row.status = sourceRow.status
                row.am_in = sourceRow.am_in
                row.am_out = sourceRow.am_out
                row.pm_in = sourceRow.pm_in
                row.pm_out = sourceRow.pm_out

            })

            renderRows()

        })
        $("#generate").click(function () {

            let period = getPeriodDates()

            if (!period) {
                alert("Select month")
                return
            }

            let start = period.start
            let end = period.end

            let employees = $("#employees").val()

            if (!start || !end) {

                alert("Select period")
                return

            }

            if (!employees) {

                alert("Select employees")
                return

            }

            let dates = generateDates(start, end)

            dtrData = {}

            employees.forEach(emp => {

                dtrData[emp] = dates.map(d => defaultRow(d))

            })

            renderEmployeeTabs()

            currentEmployee = employees[0]

            renderRows()

        })

        $(document).on("click", ".employee-tab", function () {

            $(".employee-tab").removeClass("btn-primary").addClass("btn-secondary")

            $(this).removeClass("btn-secondary").addClass("btn-primary")

            currentEmployee = $(this).data("emp")

            renderRows()

        })

        function applyStatusLogic(tr, val) {

            let am_in = tr.find(".am_in")
            let am_out = tr.find(".am_out")
            let pm_in = tr.find(".pm_in")
            let pm_out = tr.find(".pm_out")

            function enableAll() {

                am_in.prop("disabled", false)
                am_out.prop("disabled", false)
                pm_in.prop("disabled", false)
                pm_out.prop("disabled", false)

            }

            function disableAM() {

                am_in.prop("disabled", true).val("")
                am_out.prop("disabled", true).val("")

                pm_in.prop("disabled", false).val("1:00")
                pm_out.prop("disabled", false).val("5:00")

            }

            function disablePM() {

                pm_in.prop("disabled", true).val("")
                pm_out.prop("disabled", true).val("")

                am_in.prop("disabled", false).val("8:00")
                am_out.prop("disabled", false).val("12:00")

            }

            function disableAll() {

                am_in.prop("disabled", true).val("")
                am_out.prop("disabled", true).val("")
                pm_in.prop("disabled", true).val("")
                pm_out.prop("disabled", true).val("")

            }

            switch (val) {

                case "":
                    enableAll()
                    break

                case "ABSENT_AM":
                case "VL_AM":
                case "SL_AM":
                    disableAM()
                    break

                case "ABSENT_PM":
                case "VL_PM":
                case "SL_PM":
                    disablePM()
                    break

                case "HOLIDAY":
                case "NO_DUTY":
                case "SUNDAY":
                case "ABSENT":
                case "VL":
                    disableAll()
                    break

            }

        }

        $(document).on("change", ".status", function () {

            let tr = $(this).closest("tr")
            let val = $(this).val()

            let rowIndex = tr.data("row")

            dtrData[currentEmployee][rowIndex].status = val

            applyStatusLogic(tr, val)

        })
        $(document).on("input", ".am_in,.am_out,.pm_in,.pm_out", function () {

            let rowIndex = $(this).closest("tr").data("row")

            let field = $(this).attr("class").split(" ")[1]

            dtrData[currentEmployee][rowIndex][field] = $(this).val()

        })

        /*
        FORMAT DATA FOR PRINT TEMPLATE
        */

        function formatForPrinting(rows) {

            let formatted = []

            rows.forEach(r => {

                let row = {
                    day: r.day
                }

                switch (r.status) {

                    case "SUNDAY":
                        row.mode = "full"
                        row.description = "SUNDAY"
                        break

                    case "HOLIDAY":
                        row.mode = "full"
                        row.description = "HOLIDAY"
                        break

                    case "NO_DUTY":
                        row.mode = "full"
                        row.description = "NO DUTY"
                        break

                    case "VL":
                        row.mode = "full"
                        row.description = "VACATION LEAVE"
                        break

                    case "VL_AM":
                        row.mode = "am"
                        row.pm_in = r.pm_in
                        row.pm_out = r.pm_out
                        row.description = "VL"
                        break

                    case "VL_PM":
                        row.mode = "pm"
                        row.am_in = r.am_in
                        row.am_out = r.am_out
                        row.description = "VL"
                        break

                    case "ABSENT":
                        row.mode = "full"
                        row.description = "ABSENT"
                        break

                    case "ABSENT_AM":
                        row.mode = "am"
                        row.pm_in = r.pm_in
                        row.pm_out = r.pm_out
                        row.description = "ABSENT"
                        break

                    case "ABSENT_PM":
                        row.mode = "pm"
                        row.am_in = r.am_in
                        row.am_out = r.am_out
                        row.description = "ABSENT"
                        break

                    case "SL_AM":
                        row.mode = "am"
                        row.pm_in = r.pm_in
                        row.pm_out = r.pm_out
                        row.description = "SL"
                        break

                    case "SL_PM":
                        row.mode = "pm"
                        row.am_in = r.am_in
                        row.am_out = r.am_out
                        row.description = "SL"
                        break

                    default:

                        row.mode = "normal"

                        row.am_in = r.am_in
                        row.am_out = r.am_out
                        row.pm_in = r.pm_in
                        row.pm_out = r.pm_out

                }

                formatted.push(row)

            })

            return formatted

        }

        $("#prepareData").click(function () {

            let result = {}

            Object.keys(dtrData).forEach(emp => {
                result[emp] = formatForPrinting(dtrData[emp])
            })

            let period = getPeriodDates()

            if (!period) {
                alert("Please select month and cutoff")
                return
            }

            let payload = {

                start: formatDate(period.start),
                end: formatDate(period.end),

                branch: $('#branch').val(),

                employees: result

            }

            $("#dtr_payload").val(JSON.stringify(payload))

            $("#printForm").submit()

        })

    </script>
</body>

</html>
