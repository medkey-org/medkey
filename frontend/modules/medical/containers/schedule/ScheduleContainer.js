import { connect } from 'react-redux';
import Schedule from '../../components/schedule/Schedule';
import {changeDate, changeSpeciality, changeService} from '../../actions/schedule/actions';

const mapStateToProps = (state) => {
    return {
        employees: state.employees,
        specialities: state.specialities.specialities,
        isFetching: state.isFetching,
        services: state.services.services,
        filters: state.filters
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        onChangeDate: (e) => {
            dispatch(changeDate(e));
        },
        onChangeSpeciality: (e) => {
            dispatch(changeSpeciality(e));
        },
        onChangeService: (e) => {
            // dispatch(changeService(e));
        }
    }
};

const ScheduleContainer = connect(
    mapStateToProps,
    mapDispatchToProps
)(Schedule);

export default ScheduleContainer;