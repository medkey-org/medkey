import { connect } from 'react-redux';
import Schedule from '../../components/schedule/Schedule';
import {submitFilter, changeDate, changeSpeciality, changeService, attendanceEhr} from '../../actions/schedule/actions';

const mapStateToProps = (state) => {
    return {
        scheduleDuration: state.scheduleDuration,
        schedule: state.schedule,
        specialities: state.specialities.specialities,
        isFetching: state.isFetching,
        services: state.services.services,
        filters: state.filters,
        ehr: state.ehr,
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        onChangeDate: (e) => {
            dispatch(changeDate(e));
        },
        onChangeSpeciality: (e) => {
            // document.getElementsByClassName('select-service')[0].dispatchEvent(new Event('clear'));
            dispatch(changeSpeciality(e));
        },
        onChangeService: (e) => {
            dispatch(changeService(e));
        },
        onSubmitFilter: (e) => {
            dispatch(submitFilter(e));
        },
        onAttendanceEhr: (e) => {
            dispatch(attendanceEhr(e));
        }
    }
};

const ScheduleContainer = connect(
    mapStateToProps,
    mapDispatchToProps
)(Schedule);

export default ScheduleContainer;