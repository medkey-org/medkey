import moment from 'moment';

// action types
// todo в отдельный файл
const TYPE_RECEIVE_EMPLOYEES = 1;
const TYPE_FETCHIHG = 2;
const TYPE_FETCH_SPECIALITIES = 5;

export function fetchSpecialities() {
    return dispatch => {
        fetch('/medical/rest/speciality/speciality-list', {
            'method': 'POST',
            'headers': {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        }).then(function (response) {
            return response.json();
        }).then(function (data) {
            dispatch({
                type: TYPE_FETCH_SPECIALITIES,
                specialities: data
            });
        });
    }
}

function fetchEmployees(date) {
    return dispatch => {
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
            dispatch({
                type: TYPE_RECEIVE_EMPLOYEES,
                date: date,
                employees: data
            });
        });
    };
}

export function changeDate(e) {
    return dispatch => {
        dispatch({
            type: TYPE_FETCHIHG,
            isFetching: true
        });
        let d = null;
        if (e === undefined) {
            d = moment();
        } else {
            d = e;
        }
        console.log(d);
        dispatch(fetchEmployees(d));
    };
}

// // patient mini-card block
// function fetchingPatient() {
//     return {
//         type: TYPE_FETCHING_PATIENT
//     };
// }

// function receivePatient(ehr) {
//     return {
//         type: TYPE_RECEIVE_PATIENT,
//         ehr: ehr
//     };
// }
//
// export function fetchPatient(e) {
//     let element = e.currentTarget;
//     return dispatch => {
//         dispatch(fetchingPatient());
//         fetch('/medical/rest/ehr/get-ehr-by-id?id=' + element.dataset.key, { // TODO host config
//             'method': 'POST',
//             'credentials': 'same-origin',
//             'headers': {
//                 'Accept': 'application/json',
//                 'Content-Type': 'application/json',
//             },
//             // 'mode': 'cors'
//         }).then(function (response) {
//             return response.json();
//         }).then(function (data) {
//             dispatch(receivePatient(data));
//         });
//     };
// }