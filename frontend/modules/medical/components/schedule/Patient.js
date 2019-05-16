import React from 'react';
import './Patient.css';

// deprecated
class Patient extends React.Component {
    componentDidUpdate() { // todo in render func
        if (this.props.isFetching) {
            $('.b-patient').loading('start');
        } else {
            $('.b-patient').loading('stop');
        }
    }
    render() {
        return (
            <div className="b-patient">
                <h5 className="patient-header">Информация о пациенте</h5>
                <div className="patient-card">
                    <span className="patient-title">Пациент: </span>
                    <span className="patient-fio">
                        {
                            this.props.ehr && this.props.ehr.patient ?
                                this.props.ehr.patient.last_name + ' ' + this.props.ehr.patient.first_name + ' ' + this.props.ehr.patient.middle_name : ""
                        }
                    </span>
                    <br />
                    <span className="patient-title-policy">Полисы: </span>
                    <span className="patient-policy">
                        {
                            this.props.ehr && this.props.ehr.patient && this.props.ehr.patient.policies ? this.props.ehr.patient.policies.map(policy =>
                            <span>
                                Номер: {policy.number} (страховая: {policy.insurance.title}, дата действия: {policy.issue_date} - {policy.expiration_date})
                            </span>) : ""
                        }
                    </span>
                    
                </div>
            </div>
        );
    }
}

export default Patient;