export default function arenda(state = [], action) {
  switch (action.type) {
    case 'FETCH_ROOMS':
      return [...state, action.payload]
    default:
      return state
  }
}