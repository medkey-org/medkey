
// action types
// todo в отдельный файл
const TYPE_FETCH_EMPLOYEES = 1;
const TYPE_FETCHING = 2;
const TYPE_FETCH_SPECIALITIES = 5;
const TYPE_FETCH_SERVICES = 6;

export function fetchSpecialities() {
    return dispatch => {
        dispatch(fetching(true));
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
        }).finally(function () {
            dispatch(fetching(false));
        });
    }
}

// filter date
export function changeDate(date) {
    return dispatch => {
        dispatch(fetchEmployees(date));
    };
}

export function changeSpeciality(specialityId) {
    if (!specialityId.hasOwnProperty('value')) {
        console.warn('Warn');
        return null;
    }
    return dispatch => {
        dispatch(fetchServices(specialityId.value));
    };
}

export function fetching(isFetching = false) {
    return {
        type: TYPE_FETCHING,
        isFetching: isFetching
    };
}

function fetchServices(specialityId) {
    return dispatch => {
        dispatch(fetching(true));
        fetch('/medical/rest/service/speciality-list?speciality_id=' + specialityId, {
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
                type: TYPE_FETCH_SERVICES,
                services: data
            });
        }).finally(function () {
            dispatch(fetching(false));
        });
    };
}

function fetchEmployees(date) {
    return dispatch => {
        dispatch(fetching(true));
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
                type: TYPE_FETCH_EMPLOYEES,
                filterDate: date,
                employees: data
            });
        }).finally(function () {
            dispatch(fetching(false));
        });
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