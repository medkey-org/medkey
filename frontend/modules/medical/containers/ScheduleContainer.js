import { connect } from 'react-redux';
import Schedule from '../components/Schedule';
import { changeDate, fetchPatient } from '../actions/actions';

const mapStateToProps = (state) => {
    return state.employees;
};

const mapDispatchToProps = (dispatch) => {
    return {
        onChangeDate: (e) => {
            dispatch(changeDate(e));
        },
        onClickPatient: (e) => {
            dispatch(fetchPatient(e));
        }
    }
};

const ScheduleContainer = connect(
    mapStateToProps,
    mapDispatchToProps
)(Schedule);

export default ScheduleContainer;