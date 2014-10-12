<?php
/**
* FEROS PHP Framework
* @author feros<admin@feros.com.cn>
* @copyright ©2014 feros.com.cn
* @link http://www.feros.com.cn
* @version 2.0.1
*@view /Users/feisanliang/Documents/开发项目/erp2/application/view/home/calendar.html
*/
?><?php echo $this->fetch('public/head');?>
<link rel="stylesheet" type="text/css" href="<?php echo $this->url->base('assets/calendar/fullcalendar.min.css');?>" />
<link href='<?php echo $this->url->base('assets/calendar/fullcalendar.print.css');?>' rel='stylesheet' media='print' />
<style>

    #script-warning {
        display: none;
        background: #eee;
        border-bottom: 1px solid #ddd;
        padding: 0 10px;
        line-height: 40px;
        text-align: center;
        font-weight: bold;
        font-size: 12px;
        color: red;
    }

    #loading {
        display: none;
        position: absolute;
        top: 10px;
        right: 10px;
    }

</style>

</head>
<body>
    <div class="container-fluid">
        <div id='loading'>loading...</div>

        <div id='calendar'></div>
    </div>

    <?php echo $this->fetch('public/js');?>
    <script type="text/javascript" src="<?php echo $this->url->base('assets/calendar/lib/moment.min.js');?>" ></script>
    <script type="text/javascript" src="<?php echo $this->url->base('assets/calendar/fullcalendar.min.js');?>" ></script>
    <script type="text/javascript" src="<?php echo $this->url->base('assets/calendar/lang-all.js');?>" ></script>


    <script type="text/javascript">
        $(document).ready(function () {
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                defaultView: 'month',
                defaultDate: "<?php echo date('Y-m-d');?>",
                editable: true,
                eventLimit: true,
                lang: 'zh-cn',
                timezone: 'PRC',
                selectable: true,
                selectHelper: true,
                select: function (start, end, jsEvent, view) {
                    $.scojs_modal({title: '新增日程', remote: "<?php echo $this->url->site('home/calendar_add');?>?start=" + start + '&end=' + end}).show();
                    /* var title = prompt('Event Title:');
                     var eventData;
                     if (title) {
                     eventData = {
                     title: title,
                     start: start,
                     end: end
                     };
                     $('#calendar').fullCalendar('renderEvent', eventData, true); // stick? = true
                     }
                     $('#calendar').fullCalendar('unselect');
                     *///
                },
                events: {
                    url: "<?php echo $this->url->site('home/calendar_json');?>",
                    error: function () {
                        $('#script-warning').show();
                    }
                },
                loading: function (bool) {
                    $('#loading').toggle(bool);
                },
                eventDrop: function (event, delta, revertFunc, jsEvent, ui, view) {
                    ajax_post("<?php echo $this->url->site('home/calendar_drag');?>", 'id=' + event.id + '&start=' + event.start);
                }, eventClick: function (calEvent, jsEvent, view) {
                    $.scojs_modal({title: '修改日程', remote: "<?php echo $this->url->site('home/calendar_edit');?>?id=" + calEvent.id}).show();

                }
            });
        });
    </script>

</body>
</html>