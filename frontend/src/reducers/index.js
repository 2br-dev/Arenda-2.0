import { combineReducers } from 'redux'
import renters from './renters'
import contracts from './contracts'
import invoices from './invoices'

export default combineReducers({
  renters,
  contracts,
  invoices
})