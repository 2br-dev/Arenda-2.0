import { combineReducers } from 'redux'
import renters from './renters'
import contracts from './contracts'
import rooms from './rooms'

export default combineReducers({
  renters,
  contracts,
  rooms
})