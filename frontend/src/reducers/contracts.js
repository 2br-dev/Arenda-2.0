export default function arenda(state = [], action) {
  switch (action.type) {
    case 'FETCH_CONTRACTS':
      return [...action.payload]
    case 'FETCH_ALL_CONTRACTS':
      return [...action.payload]  
    default:
      return state
  }
}