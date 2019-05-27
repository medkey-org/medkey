import { combineReducers } from 'redux';

const employees = (state = {}, action) => {
    if (action.type === 1) {
        return Object.assign({}, state, {
            employees: action.employees || [],
            filterDate: action.filterDate,
        });
    }
    return state;
};

const isFetching = (state = false, action) => {
    if (action.type === 2) {
        return action.isFetching || false;
    }
    return state;
};

// const ehr = (state = {}, action) => {
//     if (action.type === 3) {
//         return Object.assign({}, state, {
//             ehr: action.ehr,
//             isFetching: action.isFetching,
//         });
//     } else if (action.type === 2) {
//         return Object.assign({}, state, {
//             isFetching: action.isFetching,
//         });
//     }
//     return state;
// };

const specialities = (state = {}, action) => {
    if (action.type === 5) {
        return Object.assign({}, state, {
            specialities: action.specialities || [],
        });
    }
    return state;
};

const services = (state = {}, action) => {
    if (action.type === 6) {
        return Object.assign({}, state, {
            services: action.services || [],
        })
    }
    return state;
};

const filters = (state = {}, action) => {
    if (action.type === 7) {
        return Object.assign({}, state, {
            specialityId: action.value || [],
        })
    }
    if (action.type === 8) {
        return Object.assign({}, state, {
            serviceId: action.value || [],
        })
    }
    if (action.type === 9) {
        return Object.assign({}, state, {
            date: action.value || '',
        })
    }
    if (action.type === 10) {
        return Object.assign({}, state, {
            serviceId: [],
        })
    }
    return state;
};

const reducerApp = combineReducers({
    employees,
    specialities,
    services,
    filters,
    isFetching
});

const initWorkplaceState = {
    employees: {
        employees: [], filterDate: new Date()
    },
    specialities: {
        specialities: []
    },
    services: {
        services: []
    },
    filters: {
        specialityId: [],
        serviceId: [],
        date: '',
    },
    isFetching: false
};
export { reducerApp, initWorkplaceState };