// deprecated
import { connect } from 'react-redux';
import Patient from '../../components/schedule/Patient';

const mapStateToProps = (state) => {
    return state.ehr;
};

const mapDispatchToProps = (dispatch) => {
    return {
        // onClickPatient: (e) => {
        //     dispatch();
        // }
    }
};

const PatientContainer = connect(
    mapStateToProps,
    mapDispatchToProps
)(Patient);

export default PatientContainer;