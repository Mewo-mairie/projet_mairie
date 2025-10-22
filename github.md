# 1) Se mettre à jour
git checkout main
git pull --ff-only
 
# 2) Mettre à jour sa branche perso depuis main
git checkout user/francois (ou /emmanuel ou /jessica)
git pull --rebase          # récupère vos commits distants éventuels
git rebase main            # remet vos commits au-dessus de main (propre)
# si conflits: corriger, puis: git add . && git rebase --continue
 
# 3) Travailler
# ... modifications ...
git add .
git commit -m "feat: ..."
 
# 4) Pousser
git push