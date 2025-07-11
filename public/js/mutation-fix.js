/**
 * Fix for Deprecated DOM Mutation Events
 * 
 * This script patches the EventTarget.prototype to intercept and fix deprecated
 * DOM mutation event listeners like DOMNodeInserted.
 * It replaces them with a modern MutationObserver implementation.
 */

(function() {
    // Store the original addEventListener method
    const originalAddEventListener = EventTarget.prototype.addEventListener;
    
    // Create a map to store MutationObservers
    const observers = new WeakMap();
    
    // Replace the addEventListener method with our custom implementation
    EventTarget.prototype.addEventListener = function(type, listener, options) {
        // If this is a DOMNodeInserted listener, replace it with MutationObserver
        if (type === 'DOMNodeInserted') {
            console.warn('DOMNodeInserted is deprecated. Using MutationObserver instead.');
            
            // Create a MutationObserver that triggers the original listener
            const observer = new MutationObserver((mutations) => {
                for (const mutation of mutations) {
                    if (mutation.type === 'childList') {
                        for (const node of mutation.addedNodes) {
                            // Create a synthetic event similar to DOMNodeInserted
                            const event = new CustomEvent('DOMNodeInserted', {
                                bubbles: true,
                                cancelable: true,
                                detail: { relatedNode: mutation.target }
                            });
                            
                            // Set the target property on the event
                            Object.defineProperty(event, 'target', {
                                writable: false,
                                value: node
                            });
                            
                            // Call the original listener with our synthetic event
                            listener.call(this, event);
                        }
                    }
                }
            });
            
            // Start observing
            observer.observe(this, { 
                childList: true,
                subtree: true 
            });
            
            // Store the observer in our WeakMap
            observers.set(listener, observer);
            
            // Return early - we don't need to call the original method
            return;
        }
        
        // For all other event types, use the original method
        return originalAddEventListener.call(this, type, listener, options);
    };
    
    // Also patch removeEventListener to clean up MutationObservers
    const originalRemoveEventListener = EventTarget.prototype.removeEventListener;
    
    EventTarget.prototype.removeEventListener = function(type, listener, options) {
        if (type === 'DOMNodeInserted' && observers.has(listener)) {
            // Disconnect the observer and remove it from our map
            const observer = observers.get(listener);
            observer.disconnect();
            observers.delete(listener);
            return;
        }
        
        // For all other event types, use the original method
        return originalRemoveEventListener.call(this, type, listener, options);
    };
})(); 