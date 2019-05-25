import React from 'react';
import './Schedule.css';
import ScheduleColumn from "./ScheduleColumn";
import DatePicker from 'react-datepicker';
import DatePickerInput from './DatePickerInput';
import "react-datepicker/dist/react-datepicker.css";
import Select from 'react-select';
import ReactDOM from 'react-dom';

class Schedule extends React.Component {
    constructor(props) {
        super(props);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    componentDidUpdate() { // todo in render func
        if (this.props.isFetching) {
            $(ReactDOM.findDOMNode(this)).loading('start');
        } else {
            $(ReactDOM.findDOMNode(this)).loading('stop');
        }
    }

    handleSubmit(e) {
        e.preventDefault();
        console.log(this.props.filters.specialityId);
        console.log(this.props.filters.serviceId);
        console.log(this.props.filters.date);
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
        const { selectedSpecialityOption } = this.props.filters.specialityId;
        const { selectedServiceOption } = this.props.filters.serviceId;
        return (
            <div className="b-schedule">
                <div className="filter">
                    <form action="medical/rest/schedule/..." onSubmit={this.handleSubmit}>
                    Специальность&nbsp;
                    <Select
                        className="select-speciality"
                        value={selectedSpecialityOption}
                        onChange={this.props.onChangeSpeciality}
                        options={specialityOptions}
                    />
                    Услуга&nbsp;
                    <Select
                        className="select-service"
                        value={selectedServiceOption}
                        onChange={this.props.onChangeService}
                        options={serviceOptions}
                    />
                    Дата&nbsp;
                    <div className="calendar">
                        <DatePicker
                            className="select-date"
                            selected={this.props.employees.filterDate}
                            minDate={new Date()}
                            onChange={this.props.onChangeDate}
                            customInput={<DatePickerInput />}
                        />
                    </div>
                        <button type="submit">Найти</button>
                    </form>
                </div>
                <div className="schedule">
                    {this.props.employees.employees.map(employee => <ScheduleColumn employee={employee} clickPatient={this.props.onClickPatient}/>)}
                </div>
            </div>
        );
    }
}

export default Schedule;