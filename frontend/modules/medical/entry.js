import React from 'react';
import { render } from 'react-dom';
import { Provider } from 'react-redux';
import thunk from 'redux-thunk';
import { createStore,  applyMiddleware } from 'redux';
import './components/schedule/index.css';
import { reducerApp, initWorkplaceState } from './reducers/schedule/reducers';
import { changeDate, fetchSpecialities } from './actions/schedule/actions';
import ScheduleContainer from './containers/schedule/ScheduleContainer';
// import PatientContainer from './containers/schedule/PatientContainer';

// if (document.getElementById('app-workplace')) {
//     // todo придумать помодульную декомпозицию
//     let store = createStore(reducerApp, initWorkplaceState, applyMiddleware(thunk));
//     store.dispatch(changeDate());
//     render(
//         <Provider store={store}>
//             <div className="b-workplace">
//                 <PatientContainer/>
//                 <ScheduleContainer/>
//             </div>
//         </Provider>,
//         document.getElementById('app-workplace')
//     );
// }

window.registerAttendanceSchedule = function () {
    // todo auto inject
    let store = createStore(reducerApp, initWorkplaceState, applyMiddleware(thunk));
    render(
        <Provider store={store}>
            <div className="b-workplace">
                <ScheduleContainer/>
            </div>
        </Provider>,
        document.getElementById('service-list')
    );
    store.dispatch(changeDate());
    store.dispatch(fetchSpecialities());
}