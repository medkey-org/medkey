import React from 'react';
import { render } from 'react-dom';
import { Provider } from 'react-redux';
import thunk from 'redux-thunk';
import { createStore, applyMiddleware } from 'redux';
import './components/index.css';
import { reducerApp, initWorkplaceState } from './reducers/reducers';
import { changeDate } from './actions/actions';
import ScheduleContainer from './containers/ScheduleContainer';
import PatientContainer from './containers/PatientContainer';

if (document.getElementById('app-workplace')) {
    // todo придумать помодульную декомпозицию
    let store = createStore(reducerApp, initWorkplaceState, applyMiddleware(thunk));
    store.dispatch(changeDate());
    render(
        <Provider store={store}>
            <div className="b-workplace">
                <PatientContainer/>
                <ScheduleContainer/>
            </div>
        </Provider>,
        document.getElementById('app-workplace')
    );
}