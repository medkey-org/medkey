import React from 'react';
import './Schedule.css';
import ScheduleColumn from "./ScheduleColumn";
import DatePicker from 'react-datepicker';
import DatePickerInput from './DatePickerInput';
import 'react-datepicker/dist/react-datepicker.css';
import Select from 'react-select';

// const options = [
//     { value: 'chocolate', label: 'Chocolate' },
//     { value: 'strawberry', label: 'Strawberry' },
//     { value: 'vanilla', label: 'Vanilla' }
// ];

class Schedule extends React.Component {
    constructor(props) {
        super(props);
        this.handleChange = this.handleChange.bind(this);
    }

    componentDidUpdate() { // todo in render func
        if (this.props.employees.isFetching) {
            $('.b-schedule').loading('start');
        } else {
            $('.b-schedule').loading('stop');
        }
    }

    handleChange = (selectedOption) => {
        this.setState({ selectedOption });
        console.log(`Option selected:`, selectedOption);
    }

    render() {
        const { selectedOption } = {};
        let options  = [];
        let props = this.props;
        props.specialities.forEach(function (value) {
            options.push({value: value.id, label: value.title})
        });
        return (
            <div className="b-schedule">
                <div className="filter">
                    <label>
                        Специальность&nbsp;
                        <Select
                            value={selectedOption}
                            onChange={this.handleChange}
                            options={options}
                        />
                    </label>
                    <div className="calendar">
                        <DatePicker popperPlacement="left-start" customInput={<DatePickerInput />} onChange={this.props.onChangeDate} selected={this.props.employees.employees.date} dateFormat="L" locale="en-US"/>
                    </div>
                </div>
                <div className="schedule">
                    {this.props.employees.employees.map(employee => <ScheduleColumn employee={employee} clickPatient={this.props.onClickPatient}/>)}
                </div>
            </div>
        );
    }
}

export default Schedule;