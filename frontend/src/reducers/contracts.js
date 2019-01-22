export default function arenda(state = [], action) {
  switch (action.type) {
    case 'FETCH_CONTRACTS':
      return [...action.payload]
    case 'FETCH_ALL_CONTRACTS':
      return [...action.payload] 
    case 'FETCH_CONTRACTS_BY_RENTER':
      return [...action.payload]   
    case 'CLEAR_CONTRACTS':
      return [];       
    default:
      return state
  }
}