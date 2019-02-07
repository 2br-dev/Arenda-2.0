import { combineReducers } from 'redux'
import renters from './renters'
import contracts from './contracts'
import invoices from './invoices'
import user from './user'
import peni from './peni'
import balances from './balance'

export default combineReducers({
  renters,
  contracts,
  invoices,
  user,
  peni,
  balances
})