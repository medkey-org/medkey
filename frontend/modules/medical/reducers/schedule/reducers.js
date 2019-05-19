import { combineReducers } from 'redux';
import moment from "moment";

const employees = (state = {}, action) => {
    if (action.type === 1) {
        return Object.assign({}, state, {
            employees: action.employees || [],
            date: action.date,
            isFetching: false,
        });
    } else if (action.type === 2) {
        return Object.assign({}, state, {
            isFetching: action.isFetching,
        });
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

const reducerApp = combineReducers({
    employees,
    // ehr,
    specialities
});

const initWorkplaceState = {
    // ehr: {
    //     ehr: {}, isFetching: false
    // },
    employees: {
        employees: [], date: moment(), isFetching: false
    },
    specialities: {
        specialities: []
    },
};
export { reducerApp, initWorkplaceState };