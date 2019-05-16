// var Medical_AttendanceSchedule = View.extend({
//     events: {
//         'click .speciality-title': 'clickSpecialityTitle',
//     },
//     clickSpecialityTitle: function (e) {
//         console.log('init click s');
//     },
//     initialize: function (options) {
//         var _this = this;
//         View.prototype.initialize.call(_this, options);
//         console.log('init');
//     }
// });

var Medical_ListMedworkerSchedule = View.extend({
    events: {
        'click .employee-name': 'clickEmployee',
        'change input': 'changeDate'
    },
    clickEmployee: function (e) {
        var target = $(e.currentTarget);
        target.parents('.row-employee').find('.datetime').slideToggle( "slow");

    },
    changeDate: function (e) {
        var target = $(e.currentTarget);
        var date = target.val();
        var schedule = target.parents('.datetime').find('.medworker-schedule');
        schedule.data('widget').update({date: date});
    },
    initialize: function (options) {
        var _this = this;
        View.prototype.initialize.call(_this, options);
    }
});
var Medical_MedworkerSchedule = View.extend({
    events: {
        'click .employee-schedule-time': 'clickTime',
        'click .attendance-record': 'record'
    },
    record: function (e) {
        var _this = this;
        var $this = _this.$el;
        var url = '';
        var options;
        if (_this.options['attendance_id']) {
            url = application.getComponent('request').createUrl(
                '/medical/rest/attendance/cancel-by-schedule?attendanceId=' + _this.options['attendance_id'] + '&referralId=' + _this.params['config']['referralId']
            );
        } else {
            url = application.getComponent('request').createUrl('/medical/rest/attendance/create-by-schedule');
            options = {
                employeeId: _this.params['config']['employeeId'],
                ehrId: _this.params['config']['ehrId'],
                referralId: _this.params['config']['referralId'],
                datetime: _this.options['datetime'],
                cabinetId: _this.options['cabinetId'],
            };
        }
        $this.loading('start');
        application.getComponent('request').ajax(
            url,
            {
                'method': 'post',
                'contentType': 'application/json',
                'dataType': 'json',
                'data': JSON.stringify(options)
            }
        ).done(function () {
            _this.update({}, {}, false);
        }).fail(function () {
            $this.loading('stop');
        });
    },
    clickTime: function (e) {
        var _this = this;
        var $this = _this.$el;
        var target = $(e.currentTarget);
        var $recordButton = $this.find('.attendance-record').css('display', 'inline-block');
        _this.options['datetime'] = target.data('datetime');
        if (!target.hasClass('record')) {
            _this.options['attendance_id'] = null;
            _this.options['cabinetId'] = target.data('cabinet_id');
            $recordButton.text('Записать на ' + _this.options['datetime']);
        } else {
            _this.options['attendance_id'] = target.data('attendance_id');
            $recordButton.text('Отменить запись на ' + _this.options['datetime']);
        }
    },
    initialize: function (options) {
        var _this = this;
        View.prototype.initialize.call(_this, options);
    }
});