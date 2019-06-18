import React from 'react';
import { render } from 'react-dom';
import { Provider } from 'react-redux';
import thunk from 'redux-thunk';
import { createStore,  applyMiddleware } from 'redux';
import './components/schedule/index.css';
import { reducerApp, initWorkplaceState } from './reducers/schedule/reducers';
import {changeDate, fetching, fetchSpecialities, fetchEmployees} from './actions/schedule/actions';
import ScheduleContainer from './containers/schedule/ScheduleContainer';

window.registerAttendanceSchedule = function () {
    // todo auto inject
    let store = createStore(reducerApp, initWorkplaceState, applyMiddleware(thunk));
    render(
        <Provider store={store}>
            <div className="">
                <ScheduleContainer/>
            </div>
        </Provider>,
        document.getElementById('schedule')
    );
    store.dispatch(fetching());
    store.dispatch(changeDate(new Date()));
    store.dispatch(fetchSpecialities());
    store.dispatch(fetchEmployees(new Date(), [], []));
};