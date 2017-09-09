if ('IntersectionObserver' in window &&
  'IntersectionObserverEntry' in window &&
  'intersectionRatio' in window.IntersectionObserverEntry.prototype &&
  !('isIntersecting' in IntersectionObserverEntry.prototype)) {

  Object.defineProperty(window.IntersectionObserverEntry.prototype, 'isIntersecting', {
    get: function () {
      return this.intersectionRatio > 0
    }
  })
}
