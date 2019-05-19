import { connect } from 'react-redux';
import Schedule from '../../components/schedule/Schedule';
import { changeDate, fetchPatient } from '../../actions/schedule/actions';

const mapStateToProps = (state) => {
    return {
        employees: state.employees,
        specialities: state.specialities.specialities
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        onChangeDate: (e) => {
            dispatch(changeDate(e));
        },
        // onClickPatient: (e) => {
        //     dispatch(fetchPatient(e));
        // }
    }
};

const ScheduleContainer = connect(
    mapStateToProps,
    mapDispatchToProps
)(Schedule);

export default ScheduleContainer;