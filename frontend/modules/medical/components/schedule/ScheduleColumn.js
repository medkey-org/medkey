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
                {Object.entries(this.props.employee.schedule).map(([k, v]) => (
                    <div>

                        <div className="schedule__speciality">
                            {this.props.employee.speciality.title} (кабинет №: {k})
                        </div>
                        <div className="schedule__patient-record">
                                {v.map( schedule => (
                                    <div className="record-time">
                                        {schedule}
                                    </div>
                                ))}
                        </div>
                    </div>
                ))}
            </div>
        )
    }
}

export default ScheduleColumn;