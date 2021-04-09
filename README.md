# About the Cybernations Calculator

The Cybernations Calculator is a calculator for the game [Cybernations](https://www.cybernations.net/). The calculator was created by C2Talon and its first iteration was made in early 2007, was rewritten completely from scratch starting on 16 June 2007, and last updated on around June 2014. The Wayback Machine has a viewable copy of it to see how it looked when it was last live: <https://web.archive.org/web/20150517050708/http://c2t.org/cn/calc/>

I was originally never going to release the code for this. One reason for that is I had assumed that the site I was hosting it on would be up basically forever, so I saw no point in doing so. However, I have not bothered getting a host for it for quite some time now after dropping the last one. And while I am sure there is/was one or more calculators that came about to fill the void I left, I can add this to the pile for someone else to do something with. Though I have absolutely no idea how out of date this is at this point.

Another reason I was not going to release the code before is because I did not wanted to inflict the code on anyone else. I have seen much worse at this point though, so enjoy.

### "Missing" files

The `header.php` and `footer.php` files that the script "requires" are not included, as they do not contribute to the functionality of the calculator other than needing to exist. This is simply the unchanged file as it was last hosted live.

---

## Information on the calculator's functionality

All of the following is from the original post on my forum giving information about the Cybernations Calculator, with only the conversion from BBCode to markdown format.

### Description

A relative and comparative calculator for Cybernations to compare and contrast a current nation on one side with a multitude of possible changes to it on the other.

### Notes on Usage (Specifics, Limitations, and Assumptions)

* When using the copy/paste feature, it is recommended to copy from the source code of the nation summary page, instead of the old way of copying from the page you see. If you do not, and are not using Firefox, you will have to manually fill in the resources.
* While you can copy/paste the summary page of any nation, a few things will be missing:
	* If using your own nation:
		* Events
		* Nuclear Position -- unless you own nukes
		* 90% Literacy -- unless tech over 700
		* Aircraft Levels -- if tech over 500, sets to 9
		* Mars and Moon wonder efficiency and expiry
	* If using another nation, all the above will be missing, as will:
		* Tax Rate
		* Environment
		* Global Radiation
		* Happiness
		* Citizen Income
		* Mars and Moon colony population
* Events: These serve a dual purpose. You can put actual events that are affecting your nation as implied. You can also put factors that are that are not directly included on the face of the calculator if you need to, such as trade bonuses.
* Environment: When adding environment to a perfect environment (Global Radiation+1), such as when removing Border Walls, the calculator will add the full environment effect, even if your nation does not change from the perfect environment in-game. If you know this will happen, you can counteract it by using the "Event Environment" field on the future side by as much as you need.
* Government: Anarchy: Does not mean you are in the state of anarchy, just that your government selected is Anarchy. The state of anarchy and the government of Anarchy are two different things. I will probably add actual selectable anarchy effects later.
* Happiness: There is part of this equation that I do not know, thus any happiness calculated will be slightly skewed. Since happiness directly affects income, if you lose happiness your future income will be slightly overestimated, but if you gain happiness your future income will be slightly underestimated.
* Nation Strength: All calculations are done assuming no units are deployed.
* Citizen Income: If your citizens currently make exactly $10.00, it is very likely that any positive changes you make to the future side will not be calculated as they would be in-game. This is because the game sets $10.00 to be the minimum they can possibly make in-game, even if they would normally make less.
* Mars and Moon:
	* An efficiency of zero counts as not having the wonder; otherwise, the valid range for efficiency is from 50 to 100.
	* A zero in the "Expires" field still counts as having the wonder if efficiency is not zero; the range for expires is from 0 to the maximum days the wonder can be in-game (i.e. 1200 for Mars Base and 900 for other Mars wonders, 600 for Moon Base and 450 for other Moon wonders).
	* "Expires" fields do not have to be correct and you should have it match the corresponding one unless:
		* You want to see what building a wonder on the future design would yield: in this case you probably want to set the expires to maximum for the wonder on the future side.
		* You want to compare two different days: in this case only the difference between corresponding expire fields matter and not accuracy. To calculate how things will be 20 days into the future, the future side should be 20 _less_ than the current side.
	* Colony population affects nothing in the moment; will fix.

### Known Missing Calculations

* Navy: All
* Literacy: I know it is purely tech based and have some 500+ data plots of it, I just cannot seem to find the formula.
* Environment: Above 50% Literacy there is a -1 Environment change. Since I do not have the literacy formula, I cannot account for this change.
* There is a part of the Happiness equation that I do not know, so any calculated changes to happiness will be slightly skewed, but not by more than 1% in the very worst case. The less the change, the less the skewing.
* Infra tiers above the 15k one.
* Mars and Moon: colony population affects nothing in the moment; I have to work out its formula again
