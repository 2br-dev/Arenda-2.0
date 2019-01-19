import { combineReducers } from 'redux'
import renters from './renters'
import contracts from './contracts'

export default combineReducers({
  renters,
  contracts
})