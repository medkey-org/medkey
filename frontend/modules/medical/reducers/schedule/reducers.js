import { combineReducers } from 'redux';

const employees = (state = {}, action) => {
    if (action.type === 1) {
        return Object.assign({}, state, {
            employees: action.employees || [],
            date: action.date,
            isFetching: false,
        });
    } else if (action.type === 4) {
        return Object.assign({}, state, {
            isFetching: true,
        });
    }
    return state;
};
const ehr = (state = {}, action) => {
    if (action.type === 3) {
        return Object.assign({}, state, {
            ehr: action.ehr,
            isFetching: false,
        });
    } else if (action.type === 2) {
        return Object.assign({}, state, {
            isFetching: true
        });
    }
    return state;
};
const reducerApp = combineReducers({
    employees,
    ehr
});
const initWorkplaceState = {
    ehr: {
        ehr: {}, isFetching: false
    },
    employees: {
        employees: [], date: null, isFetching: false
    }
};
export { reducerApp, initWorkplaceState };