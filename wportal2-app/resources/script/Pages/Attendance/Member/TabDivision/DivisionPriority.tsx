import * as React from 'react';
import { router } from '@inertiajs/react';
import { Box } from '@mui/material';
import { E_ICON, EIcon } from '@/script/Enum/EIcon';
import { IPropsBase } from '@/script/System/System';
import { IAppTrnDivision } from '@/script/Models/App/Trn/TrnDivisionList';
import { useProgressContext } from '@/script/Provider/ProgressProvider';
import { AppTrnUserDivisionPriorityList } from '@/script/Models/App/Trn/TrnUserDivisionPriorityList';
import { E_PAGES, getPagesHref } from '@/script/Enum/Server/App/EPages';
import { Icon } from '@/script/Component/Misc/Icon';
import { E_COLOR } from '@/script/Enum/EColor';

interface IStar {
  priority: number;
  icon: EIcon;
}

interface IProps extends IPropsBase {
  trnDivision: IAppTrnDivision;
}
export const DivisionPriority: React.FC<IProps> = ({ sx, trnDivision }) => {
  const { onStart, onFinish } = useProgressContext();

  // 表示する星情報.
  const starList: IStar[] = React.useMemo(() => {
    const maxStar = 5;
    const currentStar = AppTrnUserDivisionPriorityList.getPriority(
      trnDivision.trnUserDivisionPriority,
    );
    const ret: IStar[] = [];
    for (let i = 1; i <= maxStar; ++i) {
      ret.push({
        priority: i,
        icon: i <= currentStar ? E_ICON.STAR : E_ICON.STAR_OUTLINE,
      });
    }

    return ret;
  }, [trnDivision]);

  /**
   * 星を選択した時.
   */
  const handleClickStar = (star: IStar) => {
    router.post(
      getPagesHref(E_PAGES.ATTENDANCE_DIVISION_PRIORITY),
      {
        divisionId: trnDivision.id,
        priority: star.priority,
      },
      {
        preserveScroll: true,
        onStart,
        onFinish,
      },
    );
  };

  return (
    <Box
      sx={{
        ...sx,
      }}
    >
      {starList.map(star => {
        return (
          <Icon
            sx={{
              color: E_COLOR.YELLOW,
              cursor: 'pointer',
            }}
            key={star.priority}
            icon={star.icon}
            size={32}
            onClick={() => handleClickStar(star)}
          />
        );
      })}
    </Box>
  );
};
