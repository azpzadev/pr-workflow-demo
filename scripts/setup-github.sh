#!/usr/bin/env bash
# Demo repo ကို GitHub ပေါ်တင်ပြီး branch protection ဖွင့်ပေးတဲ့ script။
# Talk မလုပ်ခင် တစ်ခါပဲ run ပါ။
set -euo pipefail

REPO_NAME="${1:-pr-workflow-demo}"
OWNER="$(gh api user --jq .login)"
SLUG="$OWNER/$REPO_NAME"

cd "$(dirname "$0")/.."

echo "==> ၁။ Repo ဆောက်ပြီး push လုပ်နေသည် ($SLUG)"
git add -A
git commit -m "Initial commit: discount service + CI" --quiet || true

if ! gh repo view "$SLUG" >/dev/null 2>&1; then
  gh repo create "$SLUG" --public --source=. --remote=origin --push
else
  git remote get-url origin >/dev/null 2>&1 || git remote add origin "https://github.com/$SLUG.git"
  git push -u origin main
fi

echo "==> ၂။ Squash merge ကိုပဲ ခွင့်ပြု၊ merge ပြီးရင် branch ဖျက်"
gh repo edit "$SLUG" \
  --enable-squash-merge \
  --enable-merge-commit=false \
  --enable-rebase-merge=false \
  --delete-branch-on-merge

echo "==> ၃။ main branch ကို ကာကွယ်နေသည်"
# approvals=0 — GitHub မှာ ကိုယ့် PR ကို ကိုယ်တိုင် approve လုပ်လို့ မရလို့။
# တစ်ယောက်တည်း demo လုပ်လို့ရအောင် ဒီလိုထားတာ။ တကယ့် team မှာ 1 ထားပါ။
# CI gate ရော direct-push ပိတ်ထားတာရော ဒီအတိုင်း ကျန်နေသေးတယ်။
# enforce_admins=true — owner ကိုယ်တိုင်တောင် တိုက်ရိုက် push မရအောင်။
# (demo ရဲ့ အဓိက punchline ဒါ။ talk ပြီးရင် ပြန်ဖြုတ်ချင်ရင် အောက်က comment ကြည့်ပါ)
gh api -X PUT "repos/$SLUG/branches/main/protection" \
  -H "Accept: application/vnd.github+json" \
  --input - <<'JSON'
{
  "required_status_checks": {
    "strict": true,
    "contexts": ["Pest tests", "Pint (code style)"]
  },
  "enforce_admins": true,
  "required_pull_request_reviews": {
    "required_approving_review_count": 0,
    "dismiss_stale_reviews": true
  },
  "restrictions": null,
  "allow_force_pushes": false,
  "allow_deletions": false,
  "required_conversation_resolution": true
}
JSON

echo
echo "ပြီးပါပြီ ✅  https://github.com/$SLUG"
echo "Protection ဖြုတ်ချင်ရင်: gh api -X DELETE repos/$SLUG/branches/main/protection"
