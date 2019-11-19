import React from 'react';

class ScheduleColumn extends React.Component {
    constructor(props) {
        super(props);
    }
    render() {
        let props = this.props;
        let times = [
            "8:30", "9:00", "9:30", "10:00", "10:30", "11:00", "11:30", "12:00",
            "12:30", "13:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:00",
            "16:30", "17:00", "17:30", "18:00","18:30","19:00","19:30","20:00","20:30",
        ];
        return (
            <div className="schedule__column">
                <div className="schedule__employee">
                    {props.employee.last_name} {props.employee.first_name.charAt(0) + '.'} {props.employee.middle_name.charAt(0) + '.'}
                </div>
                {Object.entries(props.employee.schedule).map(([k, v]) => (
                    <div>
                        <div className="schedule__speciality">
                            {props.employee.speciality.title} (кабинет №: {k})
                        </div>
                        <div className="schedule__patient-record">
                            {/*{times.map(function (time) {*/}
                            {/*    return (*/}
                            {/*        <div class="record-time">*/}

                            {/*        </div>*/}
                            {/*    )*/}
                            {/*})}*/}

                            {v.length > 0 ? v.map( schedule => (
                                <div сс = {schedule.attendance_id} className="record-time">
                                    <span>{schedule.time}</span>
                                    <span
                                        onClick={props.onAttendanceEhr}
                                        data-employee_id = {props.employee.id}
                                        data-cabinet = {k}
                                        data-date = {props.date}
                                        data-time = {schedule.time}
                                        data-ehr_id = {props.ehr.id}
                                        data-attendance_id = {schedule.attendance_id}>{schedule.patientFullName !== undefined ? ' - '+ schedule.patientFullName + '(ОТМЕНИТЬ)' : '(ЗАПИСАТЬ)'}
                                    </span>
                                </div>
                            )) : 'Нет расписания' }
                        </div>
                    </div>
                ))}
            </div>
        )
    }
}

export default ScheduleColumn;