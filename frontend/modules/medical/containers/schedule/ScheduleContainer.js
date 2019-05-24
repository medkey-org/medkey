import { connect } from 'react-redux';
import Schedule from '../../components/schedule/Schedule';
import {changeDate, changeSpeciality} from '../../actions/schedule/actions';

const mapStateToProps = (state) => {
    return {
        employees: state.employees,
        specialities: state.specialities.specialities,
        isFetching: state.isFetching,
        services: state.services
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        onChangeDate: (e) => {
            dispatch(changeDate(e));
        },
        onChangeSpeciality: (e) => {
            dispatch(changeSpeciality(e));
        }
    }
};

const ScheduleContainer = connect(
    mapStateToProps,
    mapDispatchToProps
)(Schedule);

export default ScheduleContainer;