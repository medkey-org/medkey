import moment from 'moment';

// action types
// todo в отдельный файл
const TYPE_RECEIVE_EMPLOYEES = 1;
const TYPE_FETCHING_EMPLOYEES = 4;

const TYPE_RECEIVE_PATIENT = 3;
const TYPE_FETCHING_PATIENT = 2;

// employees (schedule) block
function receiveEmployees(date, data = []) {
    return {
        type: TYPE_RECEIVE_EMPLOYEES,
        date: date,
        employees: data
    }
}

function fetchingEmployees() {
    return {
        type: TYPE_FETCHING_EMPLOYEES
    };
}

function fetchEmployees(date) {
    return dispatch => {
        dispatch(fetchingEmployees());
        fetch('/organization/rest/employee/employees-with-attendance-by-date?date=' + date, { // todo HOST config
            'method': 'POST',
            'headers': {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            // 'mode': 'cors'
        }).then(function (response) {
            return response.json();
        }).then(function (data) {
            dispatch(receiveEmployees(date, data));
        });
    };
}

export function changeDate(e) {
    let d = null;
    if (e === undefined) {
        d = moment();
    } else {
        d = e;
    }
    return dispatch => {
        dispatch(fetchEmployees(d));
    };
}

// patient mini-card block
function fetchingPatient() {
    return {
        type: TYPE_FETCHING_PATIENT
    };
}

function receivePatient(ehr) {
    return {
        type: TYPE_RECEIVE_PATIENT,
        ehr: ehr
    };
}

export function fetchPatient(e) {
    let element = e.currentTarget;
    return dispatch => {
        dispatch(fetchingPatient());
        fetch('/medical/rest/ehr/get-ehr-by-id?id=' + element.dataset.key, { // TODO host config
            'method': 'POST',
            'credentials': 'same-origin',
            'headers': {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            // 'mode': 'cors'
        }).then(function (response) {
            return response.json();
        }).then(function (data) {
            dispatch(receivePatient(data));
        });
    };
}