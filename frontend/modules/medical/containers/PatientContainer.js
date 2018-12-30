import { connect } from 'react-redux';
import Patient from '../components/Patient';

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