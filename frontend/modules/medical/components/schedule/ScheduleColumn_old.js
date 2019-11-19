import React from 'react';

class ScheduleColumn_old extends React.Component {
    constructor(props) {
        super(props);
    }
    render() {
        let props = this.props;
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
                                        data-attendance_id = {schedule.attendance_id}>{schedule.patientFullName !== undefined ? ' - '+ schedule.patientFullName + ' (отменить запись)' : ' (записать)'}
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

export default ScheduleColumn_old;