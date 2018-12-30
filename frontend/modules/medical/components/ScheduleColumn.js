import React from 'react';

class ScheduleColumn extends React.Component {
    constructor(props) {
        super(props);
    }
    render() {
        return (
            <div className="schedule__column">
                <div className="schedule__employee">
                    {this.props.employee.last_name} {this.props.employee.first_name.charAt(0) + '.'} {this.props.employee.middle_name.charAt(0) + '.'}
                </div>
                <div className="schedule__speciality">
                    {this.props.employee.speciality.title}
                </div>
                {this.props.employee.attendances.map(attendance =>
                    <div className="schedule__patient-record">
                    <span className="record-time">
                        {
                            (new Date(attendance.datetime)).getHours()
                            + ':'
                            + ((new Date(attendance.datetime)).getMinutes() < 10 ? '0' : '')
                            + (new Date(attendance.datetime)).getMinutes()
                        }
                    </span>
                        {
                            attendance.ehr.patient &&
                            <span className="patient-name" data-key={attendance.ehr.id}
                                  onClick={this.props.clickPatient}>
                                {attendance.ehr.patient.last_name} {attendance.ehr.patient.first_name} {attendance.ehr.patient.middle_name}
                            </span>
                        }
                    </div>
                )}
            </div>
        )
    }
}

export default ScheduleColumn;