# Rakayuku Feature Testing Report (Raw Tinker Output)

This report contains the raw execution output from the PHP Tinker session used to verify the financial integrity and business logic of the Rakayuku ERP system.

## Test Environment
- **Date**: 2026-05-12
- **Tool**: PHP Artisan Tinker
- **Environment**: Local Development (Laragon)

## Execution Log

```text
--- STARTING UNIT TESTING ---
Initial Balance: 49000000
Material Created: Kayu Jati Test 6a034e8d2d661
Purchase Total: 1000000
Stock after Purchase: 10.00
Avg Price after Purchase: 100000.00
Cash Balance after Purchase: 48000000
Order Created: ORDER-12052026-CUSTOMER-02
Order Payment Status: PARTIAL
Cash Balance after Order DP: 48500000
Stock after Production Usage: 8.00
Order Total Cost: 200000.00
Order Profit: 1800000.00
Stock after Residue Return: 8.50
Order Total Cost after Residue: 150000.00
Order Status after Finish: DELIVERING
Order Status after Delivery: UNPAID_DELIVERED
Order Payment Status after Final: PAID
Order Final Status: FINISHED
Final Cash Balance: 50000000
--- TESTING COMPLETED SUCCESSFULLY ---
```

## Features Verified

| Feature | Verification Result |
|---------|---------------------|
| **Initial Cash Balance** | ✅ Success (Added 10jt to current balance) |
| **Material Creation** | ✅ Success (Created with unique ID) |
| **Purchase (Stok Masuk)** | ✅ Success (Stock updated, HPP calculated, Cash updated) |
| **Order Creation (DP)** | ✅ Success (DP recorded in Cashflow, status PARTIAL) |
| **Production (Usage)** | ✅ Success (Stock reduced, total_cost & profit updated) |
| **Residue Return** | ✅ Success (Stock returned, cost reduced) |
| **Status Workflow** | ✅ Success (PENDING -> PRODUCTION -> DELIVERING -> UNPAID_DELIVERED) |
| **Final Payment** | ✅ Success (Status PAID & FINISHED, Final Cash balanced) |

## Maintenance Notes
- Fixed linter error in `ProductionService@removeResidue` by switching to static `destroy()`.
- Verified PHP 8.4 compatibility for all service methods.
- No critical bugs found during this final verification run.
