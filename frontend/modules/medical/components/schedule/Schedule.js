import React from 'react';
import './Schedule.css';
import ScheduleColumn from "./ScheduleColumn";
import DatePicker from 'react-datepicker';
import DatePickerInput from './DatePickerInput';
import "react-datepicker/dist/react-datepicker.css";
import Select from 'react-select';

class Schedule extends React.Component {

    componentDidUpdate() { // todo in render func
        if (this.props.isFetching) {
            $('.b-schedule').loading('start');
        } else {
            $('.b-schedule').loading('stop');
        }
    }

    render() {
        // const { selectedOption } = {};
        let options  = [];
        let props = this.props;
        props.specialities.forEach(function (value) {
            options.push({value: value.id, label: value.title})
        });
        return (
            <div className="b-schedule">
                <div className="filter">
                    Специальность&nbsp;
                    <Select
                        // value={selectedOption}
                        onChange={this.props.onChangeSpeciality}
                        options={options}
                    />
                    <div className="calendar">
                        <DatePicker
                            selected={this.props.employees.filterDate}
                            minDate={new Date()}
                            onChange={this.props.onChangeDate}
                            customInput={<DatePickerInput />}
                        />
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