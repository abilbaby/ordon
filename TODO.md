# Donor Score Enhancement - Admin Dashboard Features

## Plan Breakdown (Approved: Enhance all pages/UI design)

**Progress: 6/8** ✅

### Phase 1: Core Admin Dashboard (Steps 1-4)
- [x] 1. Update AdminController.php: Add avg donor scores, top donors data, ensure score_breakdown loaded
- [x] 2. Create reusable ScoreBreakdown component (resources/views/components/score-breakdown.blade.php)
- [x] 3. Enhance admin/dashboard.blade.php: 
  | - Expandable match rows with full breakdown + days
  | - New Donor Leaderboard card (top 5 avg scores)
  | - Score Legend section
  | - Better UI: Cards, charts, hover effects
- [x] 4. Test Phase 1: php artisan serve → Admin dashboard → Verify scores/leaderboard

### Phase 2: Donors Page + Polish (Steps 5-7)
- [x] 5. Enhance admin/donors.blade.php: Add Avg Score column, score badges/colors
- [x] 6. Update admin/matches.blade.php: Full breakdown column like donor/recipient views
- [ ] 7. UI/Design Polish:
  | - Consistent Tailwind: Gradients, shadows, animations
  | - Mobile responsive tables/cards
  | - Live stats refresh (optional JS)

### Phase 3: Complete & Demo (Step 8)
- [ ] 8. Final test + attempt_completion with demo command

**Notes**:
- Use existing Tailwind classes for design consistency
- No new deps needed
- Focus: Match donor/recipient detail level + admin power-user features

