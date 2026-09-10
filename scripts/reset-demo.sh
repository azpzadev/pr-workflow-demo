#!/usr/bin/env bash
# Demo ကို အစကနေ ပြန်စမ်းလို့ရအောင် ရှင်းပေးတဲ့ script။ ရှေ့တင်မတက်ခင် ၂-၃ ခါ လေ့ကျင့်ပါ။
set -euo pipefail

cd "$(dirname "$0")/.."
OWNER="$(gh api user --jq .login)"
REPO_NAME="$(basename "$PWD")"
SLUG="$OWNER/$REPO_NAME"

echo "==> ဖွင့်ထားတဲ့ PR တွေ ပိတ်နေသည်"
gh pr list --repo "$SLUG" --state open --json number --jq '.[].number' \
  | while read -r n; do gh pr close "$n" --repo "$SLUG" --delete-branch || true; done

echo "==> local feature branch တွေ ဖျက်နေသည်"
git checkout main --quiet
git branch | grep -E 'feature/' | xargs -r git branch -D || true

echo "==> main ကို remote အတိုင်း ပြန်ချိန်နေသည်"
git fetch origin --quiet
git reset --hard origin/main --quiet
git clean -fd --quiet

echo "==> DiscountService ကို မူလအခြေအနေ ပြန်ထားနေသည်"
git checkout origin/main -- app/Services/DiscountService.php tests/Unit/DiscountServiceTest.php

echo
echo "အဆင်သင့်ပါပြီ ✅  ./vendor/bin/pest နဲ့ စစ်ကြည့်ပါ"
