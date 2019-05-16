import React from 'react';
import './Schedule.css';
import ScheduleColumn from "./ScheduleColumn";
import DatePicker from 'react-datepicker';
import DatePickerInput from './DatePickerInput';
import 'react-datepicker/dist/react-datepicker.css';

class Schedule extends React.Component {
    // constructor(props) {
    //     super(props);
    //     this.state = {
    //         employees: [],
    //     };
        // this.onClick = this.onClick.bind(this);
    // }
    // onClick(e) {
    // }
    componentDidUpdate() { // todo in render func
        if (this.props.isFetching) {
            $('.b-schedule').loading('start');
        } else {
            $('.b-schedule').loading('stop');
        }
    }
    render() {
        return (
            <div className="b-schedule">
                <div className="calendar">
                    <DatePicker popperPlacement="left-start" customInput={<DatePickerInput />} onChange={this.props.onChangeDate} selected={this.props.date} dateFormat="L" locale="ru-RU"/>
                </div>
                <div className="schedule">
                    {this.props.employees.map(employee => <ScheduleColumn employee={employee} clickPatient={this.props.onClickPatient}/>)}
                </div>
            </div>
        );
    }
}

export default Schedule;