export default function arenda(state = [], action) {
  switch (action.type) {
    case 'FETCH_BALANCE_BY_ID':
      return [...action.payload]
    default:
      return state
  }
}