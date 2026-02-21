
# Dispatch, Inventory & Order Status Service: Developer Implementation Guide

This document is a comprehensive guide for implementing product-wise dispatch, inventory management, and a centralized order status service in your Laravel e-commerce platform. It covers models, flows, service design, best practices, and actionable steps for robust, auditable, and scalable order fulfillment.


---


## 1. Key Concepts & Models

### OrderStatusService (Core Service)
- Centralized service to manage, validate, and update order statuses across the system.
- Enforces allowed status transitions (state machine pattern).
- Emits events/notifications on status changes.
- Example statuses: pending, confirmed, processing, partially_dispatched, dispatched, delivered, cancelled, returned, refunded, failed.

### Order
- Represents a customer's order (parent entity).
- Has many `OrderItem` records.

### OrderItem
- Represents a single product (or variant) in an order.
- Fields: order_id, product_id, variant_id, quantity, unit_price, discount, totals, etc.

### Dispatch
- Represents each dispatch event for an order item (can be partial, multi-location, or multi-batch).
- Fields: id, order_id, order_item_id, inventory_id (store/warehouse), quantity, status (pending, dispatched, delivered, returned, cancelled, etc.), dispatch_method, dispatched_by, dispatched_at, remarks, batch_no (optional), created_at, updated_at.
- Benefits:
  - Enables granular tracking of each dispatched unit/batch/location directly in the Dispatch table.
  - Supports partial dispatch, split shipments, and returns at the item level.
  - Simplifies inventory deduction and transaction logging.

### Inventory
- Represents stock for a product/variant at a location (store/warehouse).
- Fields: product_id, product_variant_id, location_type, store_id, warehouse_id, cost_price, quantity, reserved_quantity, batch_no, status, notes.

### InventoryTransaction
- Logs all inventory changes (dispatch, return, adjustment, etc.).
- Fields: inventory_id, type, quantity, before_quantity, after_quantity, reference_type, reference_id, etc.

### Store & Warehouse
- Physical locations for inventory.

---

## 1. **Key Models Overview**

### **OrderStatusService (Planned)**
- Centralized service to manage and update order statuses.
- Provides methods to set, get, and validate order status transitions.
- Ensures consistent status updates across the system (order, dispatch, returns, etc.).
- Can emit events/notifications on status changes.
- Example statuses: pending, confirmed, processing, dispatched, delivered, cancelled, returned, refunded, etc.

### **Order**
- Represents a customer's order.
- Has many `OrderItem` records.

### **OrderItem**
- Represents a single product (or variant) in an order.
- Fields: order_id, product_id, variant_id, quantity, unit_price, discount, totals, etc.
- Relationships: belongsTo `Order`, `Product`, `ProductVariant`.

### **Dispatch**
- Represents the dispatch of a specific order item from a specific inventory (store/warehouse).
- Fields: order_id, order_item_id, inventory_id, quantity, status, dispatch_method, dispatched_by, etc.
- Relationships: belongsTo `Order`, `OrderItem`, `Inventory`, `Store`, `Warehouse`, `User` (admin).

### **Inventory**
- Represents stock for a product/variant at a location (store/warehouse).
- Fields: product_id, product_variant_id, location_type, store_id, warehouse_id, cost_price, quantity, reserved_quantity, batch_no, status, notes.
- Relationships: belongsTo `Product`, `ProductVariant`, `Store`, `Warehouse`. Has many `InventoryTransaction`.

### **InventoryTransaction**
- Logs all inventory changes (dispatch, return, adjustment, etc.).
- Fields: inventory_id, type, quantity, rate, batch_no, created_by, description, reference_type, reference_id, before_quantity, after_quantity, quantity_change.
- Relationships: belongsTo `Inventory`, optionally links to `Order` via reference_type/reference_id.

### **Store**
- Represents a physical store.
- Fields: store_name, location, store_logo, status, etc.
- Relationships: hasMany `Inventory`.

### **Warehouse**
- Represents a warehouse location.
- Fields: warehouse_name, location, status, etc.
- Relationships: hasMany `Inventory`.

---




## 2. End-to-End Flow (Single Dispatch Table)

### A. Order Placement
1. Customer places an order.
2. `Order` and `OrderItem` records are created.


### B. Dispatch Planning
1. For each `OrderItem`, determine fulfillment source(s) (store/warehouse).
2. For each fulfillment event (could be multiple per item):
  - Create a `Dispatch` record specifying order_id, order_item_id, inventory_id, quantity, method, status, etc.


### C. Dispatch Execution
1. When a `Dispatch` is marked as "dispatched":
  - Validate inventory (ensure enough stock in the selected inventory).
  - Deduct quantity from `Inventory`.
  - Create an `InventoryTransaction` (type = 'dispatch', reference_id = dispatch id).
  - Update `Dispatch` status and timestamps.
2. Update related `OrderItem` statuses as needed.
3. Use `OrderStatusService` to update order status:
  - If all items fully dispatched: status = 'dispatched'.
  - If partial: status = 'partially_dispatched'.


### D. Delivery & Completion
1. When a `Dispatch` is delivered, update its status.
2. If all `Dispatch` records for an order are delivered, update related `OrderItem` and `Order` statuses via `OrderStatusService`.


### E. Returns/Cancellations
1. On return/cancellation of a `Dispatch`:
  - Add quantity back to inventory.
  - Create an `InventoryTransaction` (type = 'return' or 'cancel', reference_id = dispatch id).
  - Update statuses using `OrderStatusService`.

---

### **A. Order Placement**
- Customer places an order; `Order` and `OrderItem` records are created.

### **B. Dispatch Planning**
- For each `OrderItem`, a `Dispatch` record is created, specifying:
  - Which inventory (store/warehouse) will fulfill it
  - Quantity, method, status, etc.

### **C. Dispatch Execution**
- When a dispatch is marked as "dispatched":
  1. Deduct the quantity from the selected `Inventory`.
  2. Create an `InventoryTransaction` record:
     - type = 'dispatch'
     - before_quantity, after_quantity, quantity_change
     - reference_type = 'dispatch', reference_id = dispatch id
  3. Update dispatch status and timestamps.

### **D. Delivery & Completion**
- When delivered, update dispatch and optionally order/item status.
- If all dispatches for an order are delivered, mark order as completed.

### **E. Returns/Cancellations**
- If a dispatch is cancelled/returned:
  1. Add quantity back to inventory.
  2. Create an `InventoryTransaction` with type = 'return' or 'cancel'.

---



## 3. Permissions & Security
- Restrict dispatch/inventory actions to authorized roles (warehouse staff, super-admin, etc.).
- Log all inventory changes for auditability.
- Use policies/gates for API and UI access control.

---

---



## 4. Admin Panel & API Endpoints
- Admins can view/manage dispatches per order item.
- Inventory and transaction history visible for each product/location.
- API endpoints:
  - Create/Update Dispatch (for partial/multi-location dispatch)
  - Update Order Status
  - Inventory checks
  - View transaction logs

---

---



## 5. Best Practices & Patterns
- Use DB transactions for all dispatch/inventory updates.
- Always validate inventory before dispatching.
- Use the `Dispatch` table for granular, auditable dispatch tracking (one row per dispatch event).
- Centralize status logic in `OrderStatusService` (state machine pattern).
- Expose inventory and dispatch status in customer/admin UIs.
- Write tests for all edge cases (partial dispatch, over-dispatch, returns, status transitions).
- Use events for notifications and downstream processing.
- Monitor and audit inventory transactions regularly.

---

---



## 6. Implementation Roadmap

### Phase 1: Core Models & Migrations
- Define models: Order, OrderItem, Dispatch, Inventory, InventoryTransaction, Store, Warehouse.
- Create migrations and seeders (Dispatch table only, no DispatchItem).

### Phase 2: Service Layer
- Implement `OrderStatusService` with allowed transitions and validation.
- Implement `DispatchService` and `InventoryService`.

### Phase 3: Business Logic & API
- Build controller logic for order placement, dispatch, returns, and status updates.
- Expose RESTful API endpoints for admin and customer UIs.

### Phase 4: UI & Notifications
- Build admin UI for dispatch/inventory management.
- Integrate notifications for status changes (email, SMS, in-app, etc.).

### Phase 5: Testing & Monitoring
- Write unit and feature tests for all flows and edge cases.
- Set up monitoring/auditing for inventory and status changes.

---

## 7. Example: OrderStatusService Interface

```php
namespace App\Services\Admin;

interface OrderStatusService
{
  public function changeStatus($order, string $status): bool;
  public function canTransition($order, string $toStatus): bool;
  public function getAllowedTransitions($order): array;
}
```

---

## 8. References & Further Reading
- Laravel Docs: Eloquent, Service Container, Events
- State Machine Patterns for PHP
- Inventory Management Best Practices

---

**Keep this doc updated as you iterate on your implementation.**
