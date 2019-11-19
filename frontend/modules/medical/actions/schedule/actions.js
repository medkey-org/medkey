// todo in file for reducers
const TYPE_FETCH_EMPLOYEES = 1;
const TYPE_FETCHING = 2;
const TYPE_FETCH_SPECIALITIES = 5;
const TYPE_FETCH_SERVICES = 6;

// filters
const TYPE_FILTER_SPECIALITY = 7;
const TYPE_FILTER_SERVICE = 8;
const TYPE_FILTER_DATE = 9;
const TYPE_CLEAR_SERVICES = 10;
const TYPE_ATTENDANCE_EHR = 11;

export function changeDate(date) {
    return dispatch => {
        dispatch(filterDate(date));
        // dispatch(fetchEmployees(date));
    };
}

function filterDate(value) {
    return {
        type: TYPE_FILTER_DATE,
        value: value
    };
}

function clearFilterService() {
    return {
        type: TYPE_CLEAR_SERVICES
    }
}

export function attendanceEhr(e) {
    return (dispatch, getState) => {
        dispatch(fetching(true));
        if (e.currentTarget.dataset.attendance_id) {
            fetch('/medical/rest/attendance/cancel-by-schedule?attendanceId=' + e.currentTarget.dataset.attendance_id, {
                'method': 'GET',
                'headers': {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
            }).then(function () {
                dispatch(fetchEmployees(
                    getState().filters.date,
                    getState().filters.specialityId,
                    getState().filters.serviceId)
                )
            });
        } else {
            fetch('/medical/rest/attendance/create-by-patient-schedule', {
                'method': 'POST',
                'headers': {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                'body': JSON.stringify({
                    date: getState().schedule.date.getFullYear() + '-' + (String(getState().schedule.date.getMonth()+1)).padStart(2, '0') + '-' + String(getState().schedule.date.getDate()).padStart(2, '0'),
                    time: e.currentTarget.dataset.time,
                    ehrId: e.currentTarget.dataset.ehr_id,
                    employeeId: e.currentTarget.dataset.employee_id,
                    cabinetNumber: e.currentTarget.dataset.cabinet,
                })
            }).then(function (response) {
                return response.json();
            }).then(function () {
                dispatch(fetchEmployees(
                    getState().filters.date,
                    getState().filters.specialityId,
                    getState().filters.serviceId)
                )
            });
        }
    }
}

export function submitFilter() {
    return (dispatch, getState) => {
        dispatch(fetchEmployees(getState().filters.date, getState().filters.specialityId, getState().filters.serviceId))
    };
}

export function changeSpeciality(specialityIds) {
    return dispatch => {
        dispatch(filterSpeciality(specialityIds));
        dispatch(clearFilterService());
        if (specialityIds.length > 0) {
            dispatch(fetchServices(specialityIds));
        } else {
            console.warn('Warn. SpecialityIds is empty');
        }
    };
}

export function changeService(serviceId) {
    return dispatch => {
        dispatch(filterService(serviceId.value));
    };
}

export function fetching(isFetching = false) {
    return {
        type: TYPE_FETCHING,
        isFetching: isFetching
    };
}

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

function fetchServices(specialityIds) {
    return dispatch => {
        dispatch(fetching(true));
        let ids = [];
        specialityIds.forEach(function (elem) {
            if (!elem.hasOwnProperty('value')) {
                console.warn('Warn own value property');
                return false;
            }
            ids.push(elem.value);
        });
        fetch('/medical/rest/service/service-list?speciality_ids=' + ids.join(', '), {
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

export function fetchEmployees(date, specialityIds, serviceIds) {
    return dispatch => {
        dispatch(fetching(true));
        fetch('/medical/rest/schedule/get-schedule', { // todo HOST config
            'method': 'POST',
            'headers': {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            'body': JSON.stringify({
                date: date,
                specialityIds: specialityIds,
                serviceIds: serviceIds
            })
        }).then(function (response) {
            return response.json();
        }).then(function (data) {
            dispatch({
                type: TYPE_FETCH_EMPLOYEES,
                // filterDate: date,
                employees: data,
                date: date
            });
        }).finally(function () {
            dispatch(fetching(false));
        });
    };
}

function filterSpeciality(value) {
    return {
        type: TYPE_FILTER_SPECIALITY,
        value: value
    };
}

function filterService(value) {
    return {
        type: TYPE_FILTER_SERVICE,
        value: value,
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