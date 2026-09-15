import * as React from 'react';
import { router } from '@inertiajs/react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { IAppTrnProject } from '@/script/Models/App/Trn/TrnProjectList';
import { E_ICON, EIcon } from '@/script/Enum/EIcon';
import { Icon } from '@/script/Component/Misc/Icon';
import { E_PAGES, getPagesHref } from '@/script/Enum/Server/App/EPages';
import { useProgressContext } from '@/script/Provider/ProgressProvider';
import { AppTrnUserProjectPriorityList } from '@/script/Models/App/Trn/TrnUserProjectPriorityList';
import { E_COLOR } from '@/script/Enum/EColor';

interface IStar {
  priority: number;
  icon: EIcon;
}

interface IProps extends IPropsBase {
  trnProject: IAppTrnProject;
}
export const ProjectPriority: React.FC<IProps> = ({ sx, trnProject }) => {
  const { onStart, onFinish } = useProgressContext();

  // 表示する星情報.
  const starList: IStar[] = React.useMemo(() => {
    const maxStar = 5;
    const currentStar = AppTrnUserProjectPriorityList.getPriority(
      trnProject.trnUserProjectPriority,
    );
    const ret: IStar[] = [];
    for (let i = 1; i <= maxStar; ++i) {
      ret.push({
        priority: i,
        icon: i <= currentStar ? E_ICON.STAR : E_ICON.STAR_OUTLINE,
      });
    }

    return ret;
  }, [trnProject]);

  /**
   * 星を選択した時.
   */
  const handleClickStar = (star: IStar) => {
    router.post(
      getPagesHref(E_PAGES.ATTENDANCE_PROJECT_PRIORITY),
      {
        projectId: trnProject.id,
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
