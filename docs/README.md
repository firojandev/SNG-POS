# SNG-POS Documentation

Welcome to the SNG-POS system documentation. This directory contains comprehensive documentation for all features and components of the system.

## Documentation Structure

```
docs/
├── README.md (this file)
├── FEATURE_DOCUMENTATION_TEMPLATE.md (template for new features)
└── features/
    ├── PAYMENT_TO_SUPPLIER.md
    └── [other feature documentation files]
```

## How to Document a New Feature

When implementing a new feature, follow these steps:

1. **Copy the Template**
   ```bash
   cp docs/FEATURE_DOCUMENTATION_TEMPLATE.md docs/features/YOUR_FEATURE_NAME.md
   ```

2. **Fill in All Sections**
   - Use the template as a guide
   - Include all relevant information
   - Add code examples where helpful
   - Document all API endpoints
   - Create testing checklists

3. **Keep It Updated**
   - Update documentation when feature changes
   - Add version history entries
   - Document breaking changes

4. **Link from Main README**
   - Add a link to your feature doc in this file
   - Keep the feature list organized

## Feature Documentation

### Payment & Financial Management
- [Payment to Supplier](features/PAYMENT_TO_SUPPLIER.md) - Manage payments to suppliers with automatic balance tracking

### [Add more categories and features as they are documented]

## Documentation Standards

### File Naming
- Use `SCREAMING_SNAKE_CASE` for feature documentation files
- Example: `PAYMENT_TO_SUPPLIER.md`, `INVENTORY_MANAGEMENT.md`

### Section Order
Follow the template structure:
1. Overview
2. Database Schema
3. File Structure
4. Business Logic
5. Backend Implementation
6. Frontend Implementation
7. API Endpoints
8. Usage Guide
9. Testing
10. Future Enhancements

### Code Examples
- Use proper syntax highlighting
- Include comments for complex logic
- Show both correct and incorrect examples when helpful

### Tables
Use markdown tables for:
- Database schemas
- API endpoints
- Method lists
- Version history

### Best Practices
- ✅ Write in clear, concise language
- ✅ Include examples and code snippets
- ✅ Document both happy path and edge cases
- ✅ Add testing checklists
- ✅ Keep version history updated
- ✅ Link to external references
- ❌ Don't assume prior knowledge
- ❌ Don't skip error handling documentation
- ❌ Don't forget to document API changes

## Contributing to Documentation

### When to Update Documentation

Update documentation when:
- Adding a new feature
- Modifying existing functionality
- Changing API endpoints
- Adding/removing database fields
- Fixing bugs that affect documented behavior
- Adding new business rules

### Review Process

1. Self-review your documentation
2. Ensure all sections are complete
3. Test all code examples
4. Verify all links work
5. Check for spelling and grammar
6. Have another developer review (if possible)

## Quick Reference

### Common Patterns

#### Server-Side Pagination
See [Payment to Supplier - getData() method](features/PAYMENT_TO_SUPPLIER.md#backend-implementation) for implementation example.

#### Balance Management
See [Payment to Supplier - Business Logic](features/PAYMENT_TO_SUPPLIER.md#business-logic) for balance calculation patterns.

#### Multi-Store Isolation
See [Payment to Supplier - Global Scopes](features/PAYMENT_TO_SUPPLIER.md#multi-store-isolation) for store filtering implementation.

#### Soft Delete
See [Payment to Supplier - Model](features/PAYMENT_TO_SUPPLIER.md#model-paymenttosupplierphp) for soft delete implementation.

## Need Help?

- Check existing feature documentation for examples
- Use the template as a starting point
- Ask the team if you're unsure about any section
- Review Laravel documentation for framework-specific features

---

**Last Updated:** October 25, 2025
**Maintained By:** Development Team
