import React from 'react';
import './Schedule.css';
import ScheduleColumn from "./ScheduleColumn";
import DatePicker, { registerLocale } from 'react-datepicker';
import ru from "date-fns/locale/ru";
import DatePickerInput from './DatePickerInput';
import "react-datepicker/dist/react-datepicker.css";
import Select from 'react-select';
import ReactDOM from 'react-dom';

registerLocale("ru", ru);

class Schedule extends React.Component {
    constructor(props) {
        super(props);
    }

    componentDidUpdate() { // todo in render func
        if (this.props.isFetching) {
            $(ReactDOM.findDOMNode(this)).loading('start');
        } else {
            $(ReactDOM.findDOMNode(this)).loading('stop');
        }
    }

    render() {
        let specialityOptions  = [];
        let serviceOptions = [];
        let props = this.props;
        props.specialities.forEach(function (value) {
            specialityOptions.push({value: value.id, label: value.title})
        });
        props.services.forEach(function (value) {
            serviceOptions.push({value: value.id, label: value.title})
        });
        const { selectedSpecialityOption } = props.filters.specialityId;
        const { selectedServiceOption } = props.filters.serviceId;
        return (
            <div className="b-schedule">
                <div className="b-schedule-filter">
                    {/*<form action="" onSubmit={this.props.onSubmitFilter}>*/}
                    Специальности&nbsp;
                    <Select
                        className="select-speciality"
                        value={selectedSpecialityOption}
                        onChange={props.onChangeSpeciality}
                        isMulti
                        options={specialityOptions}
                    />
                    Услуги&nbsp;
                    <Select
                        className="select-service"
                        value={selectedServiceOption}
                        onChange={props.onChangeService}
                        isMulti
                        options={serviceOptions}
                    />
                    Дата&nbsp;
                    <div className="calendar">
                        <DatePicker
                            className="select-date"
                            selected={props.filters.date}
                            minDate={new Date()}
                            onChange={props.onChangeDate}
                            customInput={<DatePickerInput />}
                        />
                        &nbsp;<button className="btn btn-default submit" onClick={props.onSubmitFilter}>Найти</button>
                    </div>
                    {/*</form>*/}
                </div>
                <div className="schedule">
                    {props.schedule.employees.map(employee => <ScheduleColumn scheduleDuration={props.scheduleDuration} date={props.schedule.date} ehr={props.ehr} onAttendanceEhr={props.onAttendanceEhr} employee={employee}/>)}
                </div>
            </div>
        );
    }
}

export default Schedule;